<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'bmuk_no';

    protected $fillable = [
        'bmuk_no', 'euro_id', 'oem_id', 'price', 'stock', 'low_stock_threshold', 'is_featured',
        'image', 'gallery', 'description', 'collection', 'filter_type', 'car_brand', 'model', 'specification',
    ];

    /**
     * Rendered product stills keyed by a word found in the collection name.
     *
     * @var array<string, string>
     */
    public const RENDERS = [
        'oil' => 'oil-filter',
        'fuel' => 'fuel-filter',
        'cabin' => 'cabin-filter',
        'air' => 'air-filter',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'is_featured' => 'boolean',
            'gallery' => 'array',
            'specification' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'bmuk_no';
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'bmuk_no');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'bmuk_no', 'bmuk_no');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'bmuk_no', 'bmuk_no');
    }

    /**
     * "Oil Filter for Toyota Corolla".
     */
    protected function title(): Attribute
    {
        return Attribute::get(fn () => "{$this->collection} for {$this->car_brand} {$this->model}");
    }

    /**
     * The product picture. Legacy rows point at a localhost demo file or a stock
     * placeholder, so those fall back to the render for the product's collection.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            $image = (string) $this->image;
            $isPlaceholder = $image === ''
                || Str::contains($image, ['127.0.0.1', 'localhost', 'shutterstock.com', 'demo-product']);

            return $isPlaceholder ? asset("images/products/{$this->renderName()}.webp") : $image;
        });
    }

    protected function thumbUrl(): Attribute
    {
        return Attribute::get(function () {
            $full = $this->image_url;

            return Str::startsWith($full, asset('images/products/'))
                ? Str::replaceLast('.webp', '-sm.webp', $full)
                : $full;
        });
    }

    /**
     * The main picture followed by any extra gallery pictures.
     *
     * @return array<int, string>
     */
    protected function images(): Attribute
    {
        return Attribute::get(fn () => array_values(array_unique(array_filter([
            $this->image_url,
            ...($this->gallery ?? []),
        ]))));
    }

    /**
     * Each fitment row as "1.6L, Petrol, 2014-2022".
     *
     * @return array<int, string>
     */
    protected function fitments(): Attribute
    {
        return Attribute::get(fn () => collect($this->specification ?? [])
            ->map(fn ($spec) => implode(', ', array_filter([
                $spec['engine'] ?? null, $spec['fuelType'] ?? null, $spec['year'] ?? null,
            ])))
            ->filter()
            ->unique()
            ->values()
            ->all());
    }

    protected function inStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock > 0);
    }

    protected function isLowStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock > 0 && $this->stock <= $this->low_stock_threshold);
    }

    public function renderName(): string
    {
        $collection = Str::lower((string) $this->collection);

        foreach (self::RENDERS as $word => $render) {
            if (Str::contains($collection, $word)) {
                return $render;
            }
        }

        return 'oil-filter';
    }

    #[Scope]
    protected function search(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $query) use ($term) {
            if (ctype_digit($term)) {
                $query->orWhere('bmuk_no', (int) $term);
            }

            foreach (preg_split('/\s+/', $term) as $word) {
                $like = '%'.$word.'%';
                $query->orWhere(fn (Builder $q) => $q
                    ->whereLike('collection', $like)
                    ->orWhereLike('filter_type', $like)
                    ->orWhereLike('car_brand', $like)
                    ->orWhereLike('model', $like)
                    ->orWhereLike('euro_id', $like)
                    ->orWhereLike('oem_id', $like));
            }
        });
    }

    /**
     * Products that fit a vehicle described as "1.6L, Petrol, 2014-2022".
     */
    #[Scope]
    protected function fitting(Builder $query, ?string $fitment): void
    {
        if (blank($fitment)) {
            return;
        }

        [$engine, $fuel, $year] = array_pad(array_map('trim', explode(',', $fitment)), 3, null);
        $criteria = array_filter(['engine' => $engine, 'fuelType' => $fuel, 'year' => $year], 'filled');

        if ($criteria === []) {
            return;
        }

        // One fitment row must match every part, so each driver gets its own JSON query.
        match ($query->getConnection()->getDriverName()) {
            'mysql', 'mariadb' => $query->whereRaw('json_contains(specification, ?)', [json_encode([$criteria])]),
            'pgsql' => $query->whereRaw('specification::jsonb @> ?::jsonb', [json_encode([$criteria])]),
            default => $query->whereRaw(
                'exists (select 1 from json_each(products.specification) where '
                .collect($criteria)->keys()->map(fn ($key) => "json_extract(json_each.value, '$.{$key}') = ?")->implode(' and ')
                .')',
                array_values($criteria),
            ),
        };
    }

    #[Scope]
    protected function lowStock(Builder $query): void
    {
        $query->whereColumn('stock', '<=', 'low_stock_threshold');
    }
}

<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The shop listing. Every filter lives in the URL, so results can be shared and bookmarked.
 */
class ShopCatalog extends Component
{
    use WithPagination;

    public const SORTS = [
        'relevance' => 'Most relevant',
        'newest' => 'Newest',
        'price-asc' => 'Price: low to high',
        'price-desc' => 'Price: high to low',
        'rating' => 'Best rated',
        'brand' => 'Brand A–Z',
    ];

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $collection = '';

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: '')]
    public string $brand = '';

    #[Url(except: '')]
    public string $model = '';

    #[Url(except: '')]
    public string $fit = '';

    #[Url(except: 'relevance')]
    public string $sort = 'relevance';

    #[Url(except: false)]
    public bool $inStock = false;

    #[Url(except: '')]
    public string $min = '';

    #[Url(except: '')]
    public string $max = '';

    public function updated(string $property): void
    {
        if ($property === 'brand') {
            $this->reset('model', 'fit');
        }

        if ($property === 'model') {
            $this->reset('fit');
        }

        $this->resetPage();
    }

    public function clear(string $filter): void
    {
        if ($filter === 'price') {
            $this->reset('min', 'max');
        } elseif (in_array($filter, ['q', 'collection', 'type', 'brand', 'model', 'fit', 'inStock'], true)) {
            $this->reset($filter);
            $this->updated($filter);
        }

        $this->resetPage();
    }

    public function clearAll(): void
    {
        $this->reset('q', 'collection', 'type', 'brand', 'model', 'fit', 'inStock', 'min', 'max');
        $this->resetPage();
    }

    #[Computed]
    public function products()
    {
        $query = $this->filtered()->with('reviews')->withAvg('reviews', 'rating');

        match ($this->sort) {
            'newest' => $query->latest(),
            'price-asc' => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('reviews_avg_rating'),
            'brand' => $query->orderBy('car_brand')->orderBy('model'),
            default => $query->orderByDesc('is_featured')->orderByRaw('stock = 0')->orderBy('bmuk_no'),
        };

        return $query->paginate(12);
    }

    #[Computed(persist: true)]
    public function collections(): Collection
    {
        return Product::query()->selectRaw('collection, count(*) as total')->groupBy('collection')->orderBy('collection')->pluck('total', 'collection');
    }

    #[Computed]
    public function types(): Collection
    {
        return Product::query()
            ->when($this->collection, fn (Builder $q) => $q->where('collection', $this->collection))
            ->distinct()
            ->orderBy('filter_type')
            ->pluck('filter_type');
    }

    #[Computed(persist: true)]
    public function brands(): Collection
    {
        return Product::distinct()->orderBy('car_brand')->pluck('car_brand');
    }

    #[Computed]
    public function models(): Collection
    {
        return $this->brand === '' ? collect() : Product::where('car_brand', $this->brand)->distinct()->orderBy('model')->pluck('model');
    }

    #[Computed]
    public function fitments(): Collection
    {
        if ($this->brand === '' || $this->model === '') {
            return collect();
        }

        return Product::where('car_brand', $this->brand)
            ->where('model', $this->model)
            ->get(['bmuk_no', 'specification'])
            ->flatMap(fn (Product $product) => $product->fitments)
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Active filters as removable chips: [filter key => label].
     *
     * @return array<string, string>
     */
    #[Computed]
    public function chips(): array
    {
        return array_filter([
            'q' => $this->q !== '' ? "“{$this->q}”" : null,
            'collection' => $this->collection ?: null,
            'type' => $this->type ?: null,
            'brand' => $this->brand ?: null,
            'model' => $this->model ?: null,
            'fit' => $this->fit ?: null,
            'inStock' => $this->inStock ? 'In stock' : null,
            'price' => ($this->min !== '' || $this->max !== '')
                ? trim(($this->min !== '' ? money($this->min) : '').' – '.($this->max !== '' ? money($this->max) : ''), ' –')
                : null,
        ]);
    }

    private function filtered(): Builder
    {
        return Product::query()
            ->search($this->q)
            ->when($this->collection, fn (Builder $q) => $q->where('collection', $this->collection))
            ->when($this->type, fn (Builder $q) => $q->where('filter_type', $this->type))
            ->when($this->brand, fn (Builder $q) => $q->where('car_brand', $this->brand))
            ->when($this->model, fn (Builder $q) => $q->where('model', $this->model))
            ->fitting($this->fit)
            ->when($this->inStock, fn (Builder $q) => $q->where('stock', '>', 0))
            ->when(is_numeric($this->min), fn (Builder $q) => $q->where('price', '>=', (float) $this->min))
            ->when(is_numeric($this->max), fn (Builder $q) => $q->where('price', '<=', (float) $this->max));
    }

    public function render()
    {
        return view('livewire.shop-catalog');
    }
}

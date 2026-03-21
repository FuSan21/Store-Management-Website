<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            // One URL per line in the form.
            'gallery' => collect(preg_split('/\R/', (string) $this->input('gallery_urls')))
                ->map(fn ($url) => trim($url))
                ->filter()
                ->values()
                ->all(),
            // Drop rows the admin left completely empty.
            'specification' => collect($this->input('specification', []))
                ->map(fn ($row) => array_map(fn ($value) => trim((string) $value), (array) $row))
                ->reject(fn ($row) => implode('', $row) === '')
                ->values()
                ->all(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Product|null $product */
        $product = $this->route('product');
        $ignore = $product?->bmuk_no;

        return [
            'bmuk_no' => ['required', 'integer', 'min:1', Rule::unique('products', 'bmuk_no')->ignore($ignore, 'bmuk_no')],
            'euro_id' => ['required', 'string', 'max:50', Rule::unique('products', 'euro_id')->ignore($ignore, 'bmuk_no')],
            'oem_id' => ['required', 'string', 'max:50', Rule::unique('products', 'oem_id')->ignore($ignore, 'bmuk_no')],
            'collection' => ['required', 'string', 'max:20'],
            'filter_type' => ['required', 'string', 'max:20'],
            'car_brand' => ['required', 'string', 'max:20'],
            'model' => ['required', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'stock' => [$product ? 'prohibited' : 'required', 'integer', 'min:0', 'max:100000'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:10000'],
            'is_featured' => ['boolean'],
            'image' => ['nullable', 'url:https,http', 'max:255'],
            'gallery' => ['array', 'max:8'],
            'gallery.*' => ['url:https,http', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'specification' => ['required', 'array', 'min:1', 'max:30'],
            'specification.*.engine' => ['required', 'string', 'max:20'],
            'specification.*.fuelType' => ['required', 'string', 'max:20'],
            'specification.*.year' => ['required', 'string', 'max:20', 'regex:/^\d{4}(-\d{4})?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'specification.required' => 'Add at least one engine this filter fits.',
            'specification.*.year.regex' => 'Write years as 2014-2022 or 2018.',
            'gallery.*.url' => 'Each gallery line must be a full image URL.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function productAttributes(bool $includeStock = true): array
    {
        $attributes = $this->safe()->except($includeStock ? [] : ['stock']);
        $attributes['image'] = $attributes['image'] ?? '';

        return $attributes;
    }
}

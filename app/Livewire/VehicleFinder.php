<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * "Find filters for my car": brand, then model, then engine.
 */
class VehicleFinder extends Component
{
    public string $brand = '';

    public string $model = '';

    public string $fit = '';

    public string $collection = '';

    public function updatedBrand(): void
    {
        $this->reset('model', 'fit');
    }

    public function updatedModel(): void
    {
        $this->reset('fit');
    }

    #[Computed(persist: true)]
    public function brands(): Collection
    {
        return Product::distinct()->orderBy('car_brand')->pluck('car_brand');
    }

    #[Computed(persist: true)]
    public function collections(): Collection
    {
        return Product::distinct()->orderBy('collection')->pluck('collection');
    }

    #[Computed]
    public function models(): Collection
    {
        return $this->brand === ''
            ? collect()
            : Product::where('car_brand', $this->brand)->distinct()->orderBy('model')->pluck('model');
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

    #[Computed]
    public function matches(): int
    {
        return Product::query()
            ->when($this->brand, fn ($q) => $q->where('car_brand', $this->brand))
            ->when($this->model, fn ($q) => $q->where('model', $this->model))
            ->when($this->collection, fn ($q) => $q->where('collection', $this->collection))
            ->fitting($this->fit)
            ->count();
    }

    public function find()
    {
        return $this->redirectRoute('shop', array_filter([
            'brand' => $this->brand,
            'model' => $this->model,
            'fit' => $this->fit,
            'collection' => $this->collection,
        ]));
    }

    public function render()
    {
        return view('livewire.vehicle-finder');
    }
}

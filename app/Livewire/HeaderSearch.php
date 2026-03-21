<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HeaderSearch extends Component
{
    public string $q = '';

    public function mount(): void
    {
        $this->q = (string) request()->query('q', '');
    }

    #[Computed]
    public function results()
    {
        if (mb_strlen(trim($this->q)) < 2) {
            return collect();
        }

        return Product::search($this->q)->orderByDesc('stock')->limit(6)->get();
    }

    public function submit()
    {
        return $this->redirectRoute('shop', array_filter(['q' => trim($this->q)]));
    }

    public function render()
    {
        return view('livewire.header-search');
    }
}

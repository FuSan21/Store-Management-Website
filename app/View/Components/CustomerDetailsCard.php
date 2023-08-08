<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CustomerDetailsCard extends Component
{
    public $name;
    public $pastOrderCount;
    public $email;
    public $shippingAddress;
    public $billingAddress;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->name = $data['selectedFilter'];
        $this->name = 'David Kent';
        $this->pastOrderCount = 10;
        $this->email = 'david89@gmail.com';
        $this->shippingAddress = '180 North King Street, Northhampton MA 1060';
        $this->billingAddress = '180 North King Street, Northhampton MA 1060';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.customer-details-card');
    }
}

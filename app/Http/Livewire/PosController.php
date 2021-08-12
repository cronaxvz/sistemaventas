<?php

namespace App\Http\Livewire;

use App\Models\Denomination;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Livewire\Component;

class PosController extends Component
{

    public $total,$itemsQuantity,$change,$efectivo;

    public function mount()
    {
        $this->efectivo =0;
        $this->change =0;
        $this->total =Cart::getTotal();
        $this->itemsQuantity =Cart::getTotalQuantity();
    }

    public function render()
    {

        return view('livewire.pos.pos', [
            'denominations' => Denomination::orderBy('value', 'desc')->get(),
            'cart' => Cart::getContent()->sortBy('nme')
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function ACash($value)
    {
        $this->efectivo += ($value == 0 ? $this->total : $value);
        $this->efectivo = ($this->efectivo - $this->total);
    }

    protected $listeners =[
        'scan-code' => 'ScanCode',
        'removeItem' => 'removeItem',
        'clearCart' => 'clearCart',
        'SaveSale' => 'SaveSale',
        'scan-code' => 'ScanCode',
    ]
}

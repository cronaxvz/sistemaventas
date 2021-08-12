<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'name' => 'LARAVEL Y LIVEWIRE',
            'cost' => '200',
            'price' => '3520',
            'barcode' => '75010232525',
            'stock' => '1000',
            'alerts' => '10',
            'category_id' => '1',
            'image' => 'curso.png'
        ]);
        Product::create([
            'name' => 'TORTA',
            'cost' => '500',
            'price' => '350',
            'barcode' => '75010232525',
            'stock' => '1000',
            'alerts' => '10',
            'category_id' => '1',
            'image' => 'curso.png'
        ]);
        Product::create([
            'name' => 'AREPA',
            'cost' => '2020',
            'price' => '350',
            'barcode' => '75010232525',
            'stock' => '1000',
            'alerts' => '10',
            'category_id' => '1',
            'image' => 'curso.png'
        ]);Product::create([
            'name' => 'CATALINA',
            'cost' => '5600',
            'price' => '3530',
            'barcode' => '75010232525',
            'stock' => '1000',
            'alerts' => '10',
            'category_id' => '1',
            'image' => 'curso.png'
        ]);
        Product::create([
            'name' => 'IPH0NE',
            'cost' => '600',
            'price' => '1150',
            'barcode' => '75010232525',
            'stock' => '1000',
            'alerts' => '10',
            'category_id' => '1',
            'image' => 'curso.png'
        ]);
        Product::create([
            'name' => 'PC GAMER',
            'cost' => '2400',
            'price' => '520',
            'barcode' => '75010232525',
            'stock' => '1000',
            'alerts' => '10',
            'category_id' => '1',
            'image' => 'curso.png'
        ]);
    }
}

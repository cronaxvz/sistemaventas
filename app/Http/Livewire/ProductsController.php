<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductsController extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name,$barcode,$cost,$price,$stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName;
    private $pagination = 5;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
    public function mount()
    {
        $this->componentName = 'Productos';
        $this->pageTitle = 'Listado';
        $this->categoryid = 'Elegir';
    }

    public function render()
    {
        if (strlen($this->search) > 0)
            $products = Product::join('categories as c', 'c.id', 'products.category_id')
                        ->select('products.*','c.name as category')
                        ->where('products.name', 'like', '%' . $this->search . '%')
                        ->orWhere('products.barcode', 'like', '%' . $this->search . '%')
                        ->orWhere('c.name', 'like', '%' . $this->search . '%')
                        ->orderBy('products.name', 'asc')
                        ->paginate($this->pagination);
        else
            $products = Product::join('categories as c', 'c.id', 'products.category_id')
                        ->select('products.*','c.name as category')
                        ->orderBy('products.id', 'desc')
                        ->paginate($this->pagination);


        return view('livewire.products.products', [
            'products' => $products,
            'categories' => Category::orderBy('name', 'asc')->get()
        ])
            ->extends('layouts.theme.app')
            ->section('content');

    }

    public function Store()
    {
        $rules = [
            'name' => 'required|unique:products|min:3',
            'cost' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'categoryid' => 'required|not_in:Elegir'
        ];

        $message = [
            'name.required' => 'Nombre del producto es obligatorio',
            'name.unique' => 'Ya existe el nombre del producto',
            'name.min' => 'El nombre del producto debe tener minimo 3 caracteres',
            'cost.required' => 'el Costo es requerido',
            'price.required' => 'el Price es requerido',
            'stock.required' => 'el stock es requerido',
            'alerts.required' => 'Ingresa el valor minimo en existensia',
            'categoryid.not_in' => 'elegir un nombre de categoria diferente a Elegir'
        ];

        $this->validate($rules, $message);
        $product = Product::create([
            'name' => $this->name,
            'cost' => $this->cost,
            'price' => $this->price,
            'barcode' => $this->barcode,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->categoryid
        ]);

        /* $customFileName;*/
        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/products', $customFileName);
            $product->image = $customFileName;
            $product->save();
        }
        $this->resetUI();
        $this->emit('product-added', 'Producto Resgistrada ');
    }

    public function Edit(Product $product)
    {
        $this->selected_id = $product->id;
        $this->name = $product->name;
        $this->barcode = $product->barcode;
        $this->cost = $product->cost;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->alerts = $product->alerts;
        $this->category_id = $product->categoryid;
        $this->image = null;

        $this->emit('modal-show', 'show modal');
    }

    public function Update()
    {
        $rules = [
            'name' => "required|min:3|unique:products,name,{$this->selected_id}",
            'cost' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'categoryid' => 'required|not_in:Elegir'
        ];

        $message = [
            'name.required' => 'Nombre del producto es obligatorio',
            'name.unique' => 'Ya existe el nombre del producto',
            'name.min' => 'El nombre del producto debe tener minimo 3 caracteres',
            'cost.required' => 'el Costo es requerido',
            'price.required' => 'el Price es requerido',
            'stock.required' => 'el stock es requerido',
            'alerts.required' => 'Ingresa el valor minimo en existensia',
            'categoryid.not_in' => 'elegir un nombre de categoria diferente a Elegir'
        ];

        $this->validate($rules, $message);

        $product = Product::find($this->selected_id);
        $product->update([
            'name' => $this->name,
            'cost' => $this->cost,
            'price' => $this->price,
            'barcode' => $this->barcode,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->categoryid
        ]);

        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/products', $customFileName);
            $imageName = $product->image;

            $product->image = $customFileName;
            $product->save();

            if ($imageName !=null)
            {
                if (file_exists('storage/products' . $imageName))
                {
                    unlink('storage/products' . $imageName);
                }
            }
        }
        $this->resetUI();
        $this->emit('product-updated', 'Producto Actualizada ');
    }

    public function resetUI()
    {
        $this->name = '';
        $this->cost = '';
        $this->price = '';
        $this->barcode = '';
        $this->stock = '';
        $this->alerts = '';
        $this->categoryid = 'Elegir';
        $this->image = null;
        $this->selected_id = 0;
    }
    protected $listeners =['deleteRow' => 'Destroy'];

    public function Destroy(Product $product)
    {
        /*$category = Category::find($id);*/
        $imageName = $product->image;
        $product->delete();

        if ($imageName !=null) {
            if (file_exists('storage/products/' . $imageName)) {
                unlink('storage/products/' . $imageName);
            }
        }
        $this->resetUI();
        $this->emit('product-deleted', 'Producto Eliminada');
    }
}

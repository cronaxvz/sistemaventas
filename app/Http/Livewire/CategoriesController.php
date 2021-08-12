<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CategoriesController extends Component
{
      use WithFileUploads;
      use WithPagination;

      public $name, $search, $image, $selected_id, $pageTitle, $componentName;
      private $pagination = 5;

    public function mount()
    {
        $this->componentName = 'Categorias';
        $this->pageTitle = 'Listado';
    }

     public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        if (strlen($this->search) > 0)
            $categories = Category::where('name', 'like', '%' . $this->search . '%')->paginate($this->pagination);
        else
            $categories = Category::orderBy('id', 'desc')->paginate($this->pagination);

        return view('livewire.category.categories', compact('categories'))
        ->extends('layouts.theme.app')
        ->section('content');
    }
    public function Edit($id)
    {
        $category = Category::find($id);
        $this->name = $category->name;
        $this->selected_id = $category->id;
        $this->iamge = null;

        $this->emit('show-modal', 'show modal!');
    }
    public function Store()
    {
        $rules = [
            'name' => 'required|unique:categories|min:3'
        ];

        $message = [
            'name.required' => 'Nombre de la categoria es obligatorio',
            'name.unique' => 'Ya existe el nombre de la categoria',
            'name.min' => 'El nombre de la categoria debe tener minimo 3 caracteres'
        ];

        $this->validate($rules, $message);

        $category = Category::create([
            'name' => $this->name
        ]);

        $customFileName;
        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/categories', $customFileName);
            $category->image = $customFileName;
            $category->save();
        }

        $this->resetUI();
        $this->emit('category-added', 'Categoria Resgistrada ');
    }

    public function Update()
    {
        $rules = [
            'name' => "required|min:3|unique:categories,name,{$this->selected_id}"
        ];

        $message = [
            'name.required' => 'Nombre de la categoria es obligatorio',
            'name.unique' => 'Ya existe el nombre de la categoria',
            'name.min' => 'El nombre de la categoria debe tener minimo 3 caracteres'
        ];

        $this->validate($rules, $message);

        $category = Category::find($this->selected_id);
        $category->update([
            'name' => $this->name
        ]);

        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/categories', $customFileName);
            $imageName = $category->image;

            $category->image = $customFileName;
            $category->save();

            if ($imageName !=null)
            {
                if (file_exists('storage/categories' . $imageName))
                {
                    unlink('storage/categories' . $imageName);
                }
            }
        }
        $this->resetUI();
        $this->emit('category-updated', 'Categoria Actualizada ');
    }

    public function resetUI()
    {
        $this->name ='';
        $this->image = null;
        $this->search ='';
        $this->selected_id = 0;
    }
    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];
    public function Destroy(Category $category)
    {
        /*$category = Category::find($id);*/
        $imageName = $category->image;
        $category->delete();

        if ($imageName !=null) {
            unlink('storage/categories/' . $imageName);
        }
        $this->resetUI();
        $this->emit('category-deleted', 'Categoria Eliminada');
    }
}

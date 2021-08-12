<?php

namespace App\Http\Livewire;

use App\Models\Denomination;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CoinsController extends Component
{
      use WithFileUploads;
      use WithPagination;

      public $type, $value, $search, $image, $selected_id, $pageTitle, $componentName;
      private $pagination = 5;

    public function mount()
    {
        $this->componentName = 'denominaciones';
        $this->pageTitle = 'Listado';
        $this->type = 'elegir';
    }

     public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        if (strlen($this->search) > 0)
            $coins = Denomination::where('type', 'like', '%' . $this->search . '%')->paginate($this->pagination);
        else
            $coins = Denomination::orderBy('id', 'desc')->paginate($this->pagination);

        return view('livewire.denominations.coins', compact('coins'))
        ->extends('layouts.theme.app')
        ->section('content');
    }
    public function Edit($id)
    {
        $category = Denomination::find($id);
        $this->name = $category->name;
        $this->type = $category->type;
        $this->selected_id = $category->id;
        $this->iamge = null;

        $this->emit('show-modal', 'show modal!');
    }
    public function Store()
    {
        $rules = [
            'type' => 'required|not_in:Elegir',
            'value' => 'required|unique:denominations'
        ];

        $message = [
            'type.required' => 'El tipo es requerido',
            'type.not_in' => 'Elige un valor para el tipo distinto a Elegir',
            'value.required' => 'El valor es requerido',
            'value.unique' => 'Ya existe el valor'
        ];

        $this->validate($rules, $message);

        $coins = Denomination::create([
            'type' => $this->type,
            'value' => $this->value
        ]);


        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/denominations', $customFileName);
            $coins->image = $customFileName;
            $coins->save();
        }

        $this->resetUI();
        $this->emit('item-added', 'denominacion Resgistrada ');
    }

    public function Update()
    {
        $rules = [
            'type' => 'required|not_in:Elegir',
            'value' => "required|unique:denominations,value,{$this->selected_id}"
        ];

        $message = [
            'type.required' => 'El tipo es requerido',
            'type.not_in' => 'Elige un valor para el tipo distinto a Elegir',
            'value.required' => 'El valor es requerido',
            'value.unique' => 'Ya existe el valor'
        ];

        $this->validate($rules, $message);

        $coins = Denomination::find($this->selected_id);
        $coins->update([
            'type' => $this->type,
            'value' => $this->value
        ]);

        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/denominations', $customFileName);
            $imageName = $coins->image;

            $coins->image = $customFileName;
            $coins->save();

            if ($imageName !=null)
            {
                if (file_exists('storage/denominations' . $imageName))
                {
                    unlink('storage/denominations' . $imageName);
                }
            }
        }
        $this->resetUI();
        $this->emit('item-updated', 'denominacion Actualizada ');
    }

    public function resetUI()
    {
        $this->type ='';
        $this->value ='';
        $this->image = null;
        $this->search ='';
        $this->selected_id = 0;
    }
    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];
    public function Destroy(Denomination $denomination)
    {
        /*$category = Category::find($id);*/
        $imageName = $coins->image;
        $coins->delete();

        if ($imageName !=null) {
            unlink('storage/denominations/' . $imageName);
        }
        $this->resetUI();
        $this->emit('item-deleted', 'denominacion Eliminada');
    }
}

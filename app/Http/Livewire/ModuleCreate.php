<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\Helpers;
use Illuminate\Support\Str;

//Models:
use App\Models\Module;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ModuleCreate extends Component{
    /**
     * 1. Render componente.
     * 2. Guardar módulo.
     */
    
    public $name;
    public $level;
    public $level_;
    public $color;
    public $icon;

    protected $rules = [
        'name' => 'required|string|max:255',
        'level' => 'required|numeric'
    ];

    
    // protected $messages = [
    //     'name.required' => $this->name_required,
    //     'level.required' => '.trans("textos.nivel_selec_aviso").'
    // ];

    /**
     * 1. Render componente.
     */
    public function render(){
        $levels = Helpers::levels();

        return view('livewire.module-create', compact('levels'));
    }

    /**
     * 2. Guardar módulo.
     */
    public function store(){
        $this->validate();

        $slug = Str::slug($this->name);

        $module = Module::create([
            'name' => $this->name,
            'slug' => $slug,
            'color' => $this->color,
            'icon' => $this->icon,
            'level' => $this->level   
        ]);

        //Creamos permiso del módulo:
        Permission::create(['name' => 'module_'.$module->slug]);

        //Guardar .json de módulos opcionales:
        $this->jsonOptionalModules();

        //Generar .json de módulos:
        $this->jsonModules();

        session()->flash('msg', trans('textos.modulo_creado_ok'));
        return redirect()->route('modules.edit', $module->id); 
    }
}

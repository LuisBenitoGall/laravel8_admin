<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\Helpers;
use Illuminate\Support\Str;

//Models:
use App\Models\Module;

class ModuleEdit extends Component{
    /**
     * 1. Render componente.
     * 2. Actualizar módulo.
     */
    
    public $module;
    public $tab;
    public $name;
    public $level;
    public $level_;
    public $color;
    public $icon;
    public $explanation = [];
    public $msg = false;
    public $alert = false;
    public $languages;

    protected $rules = [
        'name' => 'required|string|max:255',
        'level' => 'required|numeric',
    ];

    public function mount($module, $tab){
        $this->module = $module;
        $this->tab = $tab;
        $this->name = $module->name;
        $this->level = $module->level;
        $this->color = $module->color;
        $this->icon = $module->icon;
        $this->explanation = $module->explanation? unserialize($module->explanation):false;
    }
     
    /**
     * 1. Render componente.
     */
    public function render(){
        $levels = Helpers::levels();
        
        return view('livewire.module-edit', compact('levels'));
    }

    /**
     * 2. Actualizar módulo.
     */
    public function update(){
        $this->validate();

        $slug = Str::slug($this->name);

        $arr_explanation = [];
        //Idiomas:
        $languages = Helpers::languages();
        foreach($languages as $lang){
            if(isset($this->explanation[$lang[0]])){
                $arr_explanation[$lang[0]] = $this->explanation[$lang[0]];
            }
        }
        $str_explanation = !empty($arr_explanation)? serialize($arr_explanation):NULL;

        $this->module->update([
            'name' => $this->name,
            'slug' => $slug,
            'color' => $this->color,
            'icon' => $this->icon,
            'level' => $this->level,
            'explanation' => $str_explanation
        ]);

        session()->flash('msg', trans('textos.modulo_updated'));
    }

}

<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Helpers\Helpers;

//Models:
use App\Models\Project;

class ProjectsGrid extends Component{
    /**
     * 1. Render componente.
     * 2. Cargar nuevos registros.
     */
    
    public $totalRecords;
    public $loadRecords = 12;
    public $languages;
    public $arr_status;

    public function mount(){
        //Filtro:
        //$this->arr_status = session('status_')? session('status_'):false;

        $data = Project::companyProjects(session('currentCompany'), false);
        $this->totalRecords = $data->count();

        //Idiomas:
        $this->languages = Helpers::languages();
    }
    
    /**
     * 1. Render componente.
     */
    public function render(){
        //Filtro:
        //$this->arr_status = session('status_')? session('status_'):false;

        return view('livewire.projects-grid')->with('projects', Project::companyProjects(session('currentCompany'), $this->loadRecords, $this->arr_status));
    }

    /**
     * 2. Cargar nuevos registros.
     */
    public function loadMore(){

        //dd('entra aqui');
        $this->loadRecords += 12;
    }
}

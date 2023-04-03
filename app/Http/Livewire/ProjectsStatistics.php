<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Helpers\helpers;

//Models:
use App\Models\Project;

class ProjectsStatistics extends Component{
    /**
     * 1. Render componente.
     * 2. Proyectos por estado.
     */

    public $project_status;
    public $projects_by_status;
    public $list = [];

    /**
     * @return void
     */
    public function mount(){
        $this->project_status = helpers::projectStatus();
        $this->projects_by_status = $this->projectsByStatus();

        //Pasamos al array list el número de proyectos por cada status, incluyendo los que tengan 0 proyectos.
        foreach($this->project_status as $idx => $ps){
            $n = isset($this->projects_by_status[$idx])? $this->projects_by_status[$idx]:0;
            array_push($this->list, $n);
        }
    }
    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.projects-statistics');
    }

    /**
     * 2. Proyectos por estado.
     */
    public function projectsByStatus(){
        $data = Project::where('company_id', session('currentCompany'))
        ->groupBy('status')
        ->selectRaw('count(id) as n, status')
        ->orderBy('status', 'ASC')
        ->pluck('n', 'status');

        return $data;
    }
}

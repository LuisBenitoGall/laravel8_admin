<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Category;
use App\Models\User;

class UsersGrid extends Component{
    /**
     * 1. Render componente.
     * 2. Cargar nuevos registros.
     */
    
    public $totalRecords;
    public $loadRecords = 12;

    public function mount(){
        $data = User::companyUsers(session('currentCompany'), 1);
        $this->totalRecords = $data->count();
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.users-grid')->with('users', User::companyUsers(session('currentCompany'), 1, $this->loadRecords));
    }

    /**
     * 2. Cargar nuevos registros.
     */
    public function loadMore(){
        $this->loadRecords += 12;
    }
}

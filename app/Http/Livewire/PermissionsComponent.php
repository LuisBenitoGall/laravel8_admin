<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Functionality;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsComponent extends Component{
    /**
     * 1. Render componente.
     */

    public $functionality;
    public $permissions;
    public $role;
    public $role_permissions = [];

    public function mount($functionality, $role){
        $this->functionality = $functionality;
        $this->permissions = [];
        $this->role = $role;

        //Permisos propios del rol:
        if($this->role){
            if(empty($this->role_permissions)){
                $rp = $role->permissions;

                foreach($rp as $i){
                    array_push($this->role_permissions, $i->id);
                }
            }
        }
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        $this->permissions = Permission::select('id', 'name')
        ->where('name', 'like', $this->functionality->slug.'.%')
        ->orderBy('id', 'ASC')
        ->get();

        return view('livewire.permissions-component');
    }
}

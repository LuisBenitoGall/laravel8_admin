<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\GroupMembers;

class GroupMembersController extends Controller{
    /**
     * 1. Listado de miembros de grupo para DataTable.
     */
    
    private $option = 'grupos';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
    }

    /**
     * 1. Listado de miembros de grupo para DataTable.
     */
    public function getGroupMembersDT($group_id){
        $members = GroupMembers::select('group_members.*', 'users.name', 'users.surname')
        ->join('users', 'group_members.user_id', '=', 'users.id')
        ->where('group_members.group_id', $group_id)
        ->get();
        
        //Permisos del usuario:
        $editar = Auth::user()->can('groups.edit');

        foreach($members as $row){
            //Enviamos las fechas ya formateadas:
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            //Permisos para las acciones:
            $row->editar = $editar;
        }

        return datatables($members)->toJson();    
    }
}

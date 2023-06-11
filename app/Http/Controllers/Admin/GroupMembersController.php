<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\GroupMembers;

class GroupMembersController extends Controller{
    /**
     * 1. Listado de miembros de grupo para DataTable.
     * 2. Guardar nuevo miembro.
     * 3. Marcar / desmarcar como responsable.
     * 4. Actualizar info miembro.
     * 5. Desvincular miembro del grupo.
     */
    
    private $option = 'grupos';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'group_id' => 'required|integer',
                'member' => 'required|integer'
            ]);    

        //Validación update: 
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'group_id' => 'required|integer',
            ]); 
        }
    }

    /**
     * 1. Listado de miembros de grupo para DataTable.
     */
    public function getGroupMembersDT($group_id){
        $data = GroupMembers::select('group_members.*', 'users.name', 'users.surname')
        ->join('users', 'group_members.user_id', '=', 'users.id')
        ->where('group_members.group_id', $group_id);

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name.' '.$row->surname;
        })
        ->addColumn('alta', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);    
        })
        ->addColumn('acciones', function($row){
            return view('admin.group_members.partials.dt-actions', compact('row'));    
        })
        ->make(true);
    }

    /**
     * 2. Guardar nuevo miembro.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-member');
            return redirect('/admin/groups/'.$request->group_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $responsible = $request->responsible? true:false;

        GroupMembers::firstOrCreate(
            ['group_id' => $request->group_id, 'user_id' => $request->member],
            ['group_id' => $request->group_id, 'user_id' => $request->member, 'rol' => $request->rol, 'responsible' => $responsible]
        );

        $msg = trans('textos.miembro_creado');
        return redirect()->route('groups.edit', [$request->group_id, 'members'])->with(compact('msg'));
    }

    /**
     * 3. Marcar / desmarcar como responsable.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $member = GroupMembers::find($id);
            $member->responsible = $status;
            $member->save();

            $resp = ($member)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 4. Actualizar info miembro.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-edit-member');
            return redirect('/admin/groups/'.$request->group_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->rol){
            GroupMembers::where('id', $request->id)
            ->update(['rol' => $request->rol]);
        }

        $msg = trans('textos.miembro_updated');
        return redirect()->route('groups.edit', [$request->group_id, 'members'])->with(compact('msg')); 
    }

    /**
     * 5. Desvincular miembro del grupo.
     */
    public function destroy(GroupMembers $member){
        $member->delete();  

        $msg = trans('textos.miembro_deleted');
        return redirect()->route('groups.edit', [$member->group_id, 'members'])->with(compact('msg'));   
    }
}

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
use File;
use Carbon\Carbon;

//Models:
use App\Models\Protocol;
use App\Models\ProtocolItem;
use App\Models\User;

class ProtocolItemController extends Controller{
    /**
     * 1. Listado de items por protocolo para DataTable.
     * 2. Formulario nuevo protocolo.
     * 3. Guardar nuevo item.
     * 4. Editar item.
     * 5. Actualizar item.
     * 6. Actualizar estado de item.
     * 7. Eliminar item.
     */
    
    private $option = 'protocolos';
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
                'title' => 'required',
                'protocol' => 'required|integer'
            ]);    

        //Validación update: 
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'title' => 'required',
                'status' => 'required|integer'
            ]); 
        }
    }

    /**
     * 1. Listado de items por protocolo para DataTable.
     */
    public function getProtocolItemsDT($protocol_id){
        $data = ProtocolItem::select('protocol_items.*', 'users.name', 'users.surname')
        ->where('protocol_items.protocol_id', $protocol_id)
        ->leftJoin('users', 'protocol_items.created_by', '=', 'users.id')
        ->orderBy('protocol_items.sort', 'ASC');

        $estados = helpers::protocolStatus();

        return DataTables::of($data)
        ->addColumn('title', function($row){
            return $row->id.' - '.$row->title;
        }) 
        ->addColumn('created_at', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('author', function($row){
            return $row->name.' '.$row->surname;
        })
        ->addColumn('sort', function($row){
            return $row->sort;
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.protocols.partials.dt-status', compact('row', 'estados'));     
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.protocol_items.partials.dt-actions', compact('row', 'estados'));    
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevo protocolo.
     */
    public function create(Protocol $protocol){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.protocolo_item_nuevo',
            'module' => $this->module,
            'slug' => 'protocols'
        ]; 

        $estados = helpers::protocolStatus();

        $languages = $this->languages;

        return view('admin.protocol_items.create')->with(compact('header', 'protocol', 'estados', 'languages'));    
    }

    /**
     * 3. Guardar nuevo item.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/protocols/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Fijando orden:
        $sort_ = ProtocolItem::where('protocol_id', $request->protocol)->get();
        $sort = $sort_->count() + 1;

        $i = new ProtocolItem();
        $i->protocol_id = $request->protocol;
        $i->title = $request->title;
        $i->slug = Str::slug($request->title);
        $i->description = $request->description;
        $i->sort = $sort;
        $i->created_by = Auth::user()->id;
        $i->status = $request->status? $request->status:config('constants.PROTOCOL_STATUS_DRAFT_');
        $i->save();

        $msg = trans('textos.item_creado');
        return redirect()->route('protocols.edit', [$request->protocol, 'items'])->with(compact('msg'));
    }

    /**
     * 4. Editar item.
     */
    public function edit(ProtocolItem $item){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.protocolo_item_editar',
            'module' => $this->module,
            'slug' => 'protocols'
        ]; 

        $protocol = Protocol::find($item->protocol_id);

        $estados = helpers::protocolStatus();

        $author = User::select('id', 'name', 'surname')->find($item->created_by);

        $languages = $this->languages;

        return view('admin.protocol_items.edit')->with(compact('header', 'item', 'protocol', 'estados', 'author', 'languages'));        
    }

    /**
     * 5. Actualizar item.
     */
    public function update(Request $request, ProtocolItem $item){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/protocol-items/'.$item->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $item->title = $request->title;
        $item->slug = Str::slug($request->title);
        $item->description = $request->description;
        $item->status = $request->status? $request->status:config('constants.PROTOCOL_STATUS_DRAFT_');
        $item->save();

        $msg = trans('textos.item_updated');
        return redirect()->route('protocol-items.edit', $item->id)->with(compact('msg'));
    }

    /**
     * 6. Actualizar estado de item.
     */
    public function status(Request $request, ProtocolItem $item, $status){
        if($request->ajax()){    
            $item->status = $status;
            $item->save();

            return response()->json(true);
        }
    }

    /**
     * 7. Eliminar protocolo.
     */
    public function destroy(ProtocolItem $item){
        $item->delete();
        
        $msg = trans('textos.item_deleted');
        return redirect()->route('protocols.edit', [$item->protocol_id, 'items'])->with(compact('msg'));    
    }

    /**
     * 8. Reordenar items.
     */
    public function reorderFilesDT(Request $request){
        foreach($request->collection as $c){
            ProtocolItem::where('id', $c['id'])
            ->update(['sort' => $c['pos']]);    
        }
    }
}

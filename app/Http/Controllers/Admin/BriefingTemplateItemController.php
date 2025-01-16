<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;

//Models:
use App\Models\BriefingTemplate;
use App\Models\BriefingTemplateItem;

class BriefingTemplateItemController extends Controller{
    /**
     * 1. Listado de items para DataTable.
     * 2. Guardar nuevo item.
     * 3. Editar item.
     * 4. Actualizar item.
     * 5. Eliminar item.
     */
    
    private $option = 'presupuestos';
    private $module = 'budgets';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:budgets');
        $this->middleware('briefing_template_item_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'template_id' => 'required|integer',
                'name' => 'required|string|max:191'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:191'
            ]);
        }
    }

    /**
     * 1. Listado de items para DataTable.
     */
    public function getBriefingTemplateItemsDT($template_id){
        $data = BriefingTemplateItem::where('template_id', $template_id)
        ->orderBy('recid', 'ASC')
        ->orderBy('name', 'ASC');

        $units = helpers::timeUnits();
        
        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('block', function($row){
            $recid = $row->recid? $row->getRecid($row->recid):false;
            if($recid){
                return $recid->name;
            }
        })
        ->addColumn('qty', function($row){
            return $row->expected_qty;
        })
        ->addColumn('unit', function($row) use($units){
            if($row->time_unit){
                return $units[$row->time_unit];
            }else{
                return '';
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.briefing_template_items.partials.dt-actions', compact('row'));
        })
        ->make(true);
    }

    /**
     * 2. Guardar nuevo item.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-item');
            
            return redirect('/admin/briefing-templates/'.$request->template_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $item = new BriefingTemplateItem();
        $item->recid = $request->recid;
        $item->template_id = $request->template_id;
        $item->name = $request->name;
        $item->observations = $request->observations;
        $item->time_unit = $request->time_unit;
        $item->expected_qty = $request->expected_qty;
        $item->status = $request->status? true:false;
        $item->save();

        $msg = trans('textos.item_anadido');
        return redirect()->route('briefing-templates.edit', [$request->template_id, 'items'])->with(compact('msg'));
    }

    /**
     * 3. Editar item.
     */
    public function edit(BriefingTemplateItem $item){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.item_editar',
            'module' => $this->module,
            'slug' => 'briefing-templates'
        ];  

        $template = BriefingTemplate::select('name')->find($item->template_id);

        //Bloques para items:
        $blocks = BriefingTemplateItem::where('template_id', $item->template_id)
        ->whereNull('recid')
        ->pluck('name', 'id')
        ->toArray();

        //Unidades de tiempo:
        $time_units = helpers::timeUnits();

        $languages = $this->languages;

        return view('admin.briefing_template_items.edit')->with(compact('header', 'item', 'template', 'blocks', 'time_units', 'languages'));
    }

    /**
     * 4. Actualizar item.
     */
    public function update(Request $request, BriefingTemplateItem $item){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/briefing-template-items/'.$item->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $item->name = $request->name;
        $item->observations = $request->observations;
        $item->time_unit = $request->time_unit;
        $item->expected_qty = $request->expected_qty;
        $item->status = $request->status? true:false;
        $item->save();

        $msg = trans('textos.item_updated');
        return redirect()->route('briefing-template-items.edit', $item)->with(compact('msg'));
    }

    /**
     * 5. Eliminar item.
     */
    public function destroy(BriefingTemplateItem $item){
        $id = $item->template_id;
        $item->delete();

        $msg = trans('textos.item_deleted');
        return redirect()->route('briefing-templates.edit', [$id, 'items'])->with(compact('msg'));
    }
}

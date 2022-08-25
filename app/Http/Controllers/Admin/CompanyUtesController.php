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
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanyUtes;

class CompanyUtesController extends Controller{
    /**
     * 1. Relación de UTEs.
     * 2. Vincular empresa.
     * 3. Desvincular empresa.
     * 4. Actualizar registro UTE.
     */

    private $option = 'utes';
    private $module = 'companies';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('company_utes_post', ['only' => ['store', 'update']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'ute_id' => 'required|integer',
                'member' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer'
            ]);
        }
    }

    /**
     * 1. Relación de UTEs.
     */
    public function index(Company $ute){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.ute_empresas',
            'module' => $this->module,
            'slug' => 'companies'
        ];

        $ute_companies = Company::select('companies.id', 'companies.name', 'companies.tradename', 'companies.logo', 'companies.nif', 'company_utes.start_at', 'company_utes.finish_at', 'company_utes.id AS company_ute_id', 'company_utes.participation_percent')
        ->join('company_utes', 'companies.id', '=', 'company_utes.member_id')
        ->where('company_utes.ute_id', $ute->id)
        ->whereNull('company_utes.deleted_at')
        ->get();

        //Array de las empresas de la UTE:
        $arr = [];
        foreach($ute_companies as $row){
            array_push($arr, $row->id);
        }

        //Empresas posibles:
        $companies = Company::where('status', 1)
        ->where('is_ute', '0')
        ->whereNotIn('id', $arr)
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.company_utes.index')->with(compact('header', 'ute', 'ute_companies', 'companies'));
    }

    /**
     * 2. Vincular empresa.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-company');

            return redirect('admin/company-utes/'.$request->ute_id)
            ->withErrors($validator)
            ->withInput();    
        }

        $ute = new CompanyUtes();
        $ute->ute_id = $request->ute_id;
        $ute->member_id = $request->member;
        $ute->participation_percent = $request->participation_percent;
        $ute->save();

        $msg = trans('textos.empresa_vinculada_ok');
        return redirect()->route('company-utes.index', $request->ute_id)->with(compact('msg'));
    }

    /**
     * 3. Desvincular empresa.
     */
    public function destroy(CompanyUtes $ute){
        $ute->delete();

        $msg = trans('textos.empresa_desvinculada');
        return redirect()->route('company-utes.index', $ute->ute_id)->with(compact('msg'));
    }

    /**
     * 4. Actualizar registro UTE.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-participation');

            return redirect('admin/company-utes/'.$request->ute_id)
            ->withErrors($validator)
            ->withInput();    
        }    

        $row = CompanyUtes::find($request->id);
        $row->participation_percent = $request->participation_percent;
        $row->save();

        $msg = trans('textos.empresa_updated');
        return redirect()->route('company-utes.index', $request->ute_id)->with(compact('msg'));
    }
}

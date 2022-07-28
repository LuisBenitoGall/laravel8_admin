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
     */

    private $option = 'utes';
    private $module = 'companies';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
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

        $ute_companies = Company::select('companies.id', 'companies.name', 'companies.tradename', 'companies.logo', 'companies.nif', 'company_utes.start_at', 'company_utes.finish_at', 'company_utes.id AS company_ute_id')
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

        foreach($request->members as $row){
            $ute = new CompanyUtes();
            $ute->ute_id = $request->ute_id;
            $ute->member_id = $row;
            $ute->save();
        }

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
}

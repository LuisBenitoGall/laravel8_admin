<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanySectors;

class CompanySectorsController extends Controller{
    /**
     * 1. Relación de sectores por empresa.
     * 2. Guardar / eliminar sector por empresa.
     * 3. Relación de empresas por sector.
     */

    private $option = 'empresas';
    private $module = 'companies';

    /**
     * 1. Relación de sectores por empresa.
     */
    public function getCompanySectorsDT($company_id){
        $data = Category::select('categories.id', 'categories.name', 'categories.slug', 'categories.subcategory')
        ->join('modules', 'categories.module_id', '=', 'modules.id')
        ->where('categories.status', 1)
        ->where('modules.slug', 'companies')
        ->with('sub_category')
        ->orderBy('name', 'ASC');    

        //Sectores de la empresa:
        $categories = CompanySectors::where('company_id', $company_id)
        ->pluck('category_id')
        ->toArray();

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return ucfirst($row->name);
        })
        ->addColumn('level', function($row){
            $level = trans('textos.categoria_principal');
            if($row->subcategory > 0){
                $x = $row->getCategoryName($row->subcategory);
                $level = trans('textos.subcategoria_de').' <b>'.ucfirst($x).'</b>';    
            }
            return $level;
        })
        ->addColumn('acciones', function($row) use($categories){
            return view('admin.company_sectors.partials.dt-actions', compact('row', 'categories'));    
        })
        ->rawColumns(['level'])
        ->make(true);
    }

    /**
     * 2. Guardar / eliminar sector por empresa.
     */
    public function store(Request $request, $company_id, $category_id){
        if($request->ajax()){ 
            $exist = CompanySectors::select('id')
            ->where('company_id', $company_id)
            ->where('category_id', $category_id)
            ->first();

            if($exist){
                $exist->delete();
            }else{
                $c = new CompanySectors();
                $c->company_id = $company_id;
                $c->category_id = $category_id;
                $c->save();
            }

            return response()->json(true);
        }    
    }

    /**
     * 3. Relación de empresas por sector.
     */
    public function show($category_id){
        $data = Company::select('companies.id', 'companies.name', 'companies.tradename', 'companies.logo', 'companies.nif')
        ->join('company_sectors', 'companies.id', '=', 'company_sectors.company_id')
        ->where('companies.status', 1)
        ->where('company_sectors.category_id', $category_id)
        ->orderBy('companies.name', 'ASC')
        ->get();

        return response()->json($data);
    }
}

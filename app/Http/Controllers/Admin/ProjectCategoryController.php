<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Category;
use App\Models\ProjectCategory;

class ProjectCategoryController extends Controller{
    /**
     * 1. Relación de categorías por proyecto.
     * 2. Guardar / eliminar categoría por proyecto.
     */
    
    private $option = 'proyectos';  
    private $module = 'projects';

    public function __construct(){
        $this->middleware('module_setted:projects');
    }

    /**
     * 1. Relación de categorías por proyecto.
     */
    public function getCategoriesDT($project_id){
        $data = Category::select('categories.*')
        ->join('modules', 'categories.module_id', '=', 'modules.id')
        ->where('categories.status', 1)
        ->where('categories.company_id', session('currentCompany'))
        ->where('modules.slug', 'projects')
        ->with('sub_category')
        ->orderBy('name', 'ASC');

        //Categorías del proyecto:
        $categories = ProjectCategory::where('project_id', $project_id)
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
            return view('admin.project_categories.partials.dt-actions', compact('row', 'categories'));    
        })
        ->rawColumns(['level'])
        ->make(true);
    }

    /**
     * 2. Guardar / eliminar categoría por proyecto.
     */
    public function store(Request $request, $project_id, $category_id){
        if($request->ajax()){ 
            $exist = ProjectCategory::select('id')
            ->where('project_id', $project_id)
            ->where('category_id', $category_id)
            ->first();

            if($exist){
                $exist->delete();
            }else{
                $c = new ProjectCategory();
                $c->project_id = $project_id;
                $c->category_id = $category_id;
                $c->save();
            }

            return response()->json(true);
        }    
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\Category;
use App\Models\Module;

class CategoryController extends Controller{
    /**
     * 1. Listado de categorías por módulo.
     * 1.1. Listado de categorías por módulo para DataTable.
     * 2. Formulario nueva categoría.
     * 3. Guardar nueva categoría.
     * 4. Editar categoría.
     * 5. Actualizar categoría.
     * 6. Actualizar estado.
     */

    private $option = 'categorias';
    private $modules;
    private $entities;

    public function __construct(){
        $this->modules = [
            'product-categories' => ['products', 'productos_categorias', 'product-categories'],
            'user-categories' => ['users', 'usuarios_categorias', 'user-categories'],
            'company-sectors' => ['companies', 'empresas_sectores', 'company-sectors']
        ];
        $this->entities = [
            'products' => ['product-categories', 'productos_nueva_categoria'],
            'users' => ['user-categories', 'usuarios_nueva_categoria'],
            'companies' => ['company-sectors', 'sector_nuevo']
        ];

        $this->middleware('category_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'module_id' => 'required|integer',
                'name' => 'required|string|max:255',
                'category_father' => 'nullable|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'category_father' => 'nullable|integer'
            ]);
        }
    } 
    
    /**
     * 1. Listado de categorías por módulo.
     */
    public function index(Request $request){
        $segments = $request->segments();
        $slug = $this->modules[$segments[1]][0];

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$this->modules[$segments[1]][1],
            'module' => $slug,
            'slug' => $segments[1]
        ];

        $module = Module::select('id', 'name', 'slug', 'label')
        ->where('slug', $slug)
        ->first();

        $permission = $this->modules[$segments[1]][2];
        
        return view('admin.categories.index')->with(compact('header', 'module', 'permission'));
    }

    /**
     * 1.1. Listado de categorías por módulo para DataTable.
     */
    public function getCategoriesDT($module_id){
        //Las categorías de empresas no tienen company_id:
        $exception = Module::select('id')->where('slug', 'companies')->first();

        $data = Category::select('id', 'name', 'slug', 'translations', 'subcategory', 'status'
        )
        ->when($module_id != $exception->id, function($q){
            return $q->where('company_id', session('currentCompany'));
        })
        ->where('module_id', $module_id)
        ->orderBy('name', 'ASC');

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
        ->addColumn('acciones', function($row){
            return view('admin.categories.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['level'])
        ->make(true);
    }

    /**
     * 2. Formulario nueva categoría.
     */
    public function create(Module $module){
        $suboption = $this->entities[$module->slug][1];
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$suboption,
            'module' => $module->label,
            'slug' => $module->slug
        ];  

        $permission = false;
        
        //Obtenemos las categorías principales:
        $categories = Category::getMainCategories($module->id, session('currentCompany'));

        $entity = $this->entities[$module->slug][0];

        return view('admin.categories.create')->with(compact('header', 'module', 'permission', 'categories', 'entity'));  
    }

    /**
     * 3. Guardar nueva categoría.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/'.$request->entity)
                ->withErrors($validator)
                ->withInput();
        }

        $c = new Category();
        $c->company_id = session('currentCompany');
        $c->module_id = $request->module_id;
        $c->name = $request->name;
        $c->slug = Str::slug($request->name);
        $c->subcategory = $request->category_father? $request->category_father:'0';
        $c->status = $request->status? 1:'0';
        $c->save();

        //Devolvemos al módulo correcto:
        $module = Module::select('id', 'name', 'slug', 'label')->find($request->module_id);

        switch($module->slug){
            case 'products':
                $route = 'product-categories';
                break;
            case 'companies':
                $route = 'company-sectors';
                break;
            case 'users':
                $route = 'user-categories';
                break;
        }

        $msg = trans('textos.categoria_creado_ok');
        return redirect()->route($route.'.index')->with(compact('msg'));
    }

    /**
     * 4. Editar categoría.
     */
    public function edit(Category $category){
        $module = Module::find($category->module_id);

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.categoria_editar',
            'module' => $module->label,
            'slug' => $module->slug
        ];    

        //Obtenemos las categorías principales:
        $categories = Category::getMainCategories($module->id, session('currentCompany'));

        $entity = $this->entities[$module->slug][0];

        $permission = $this->entities[$module->slug][1];

        return view('admin.categories.edit')->with(compact('header', 'module', 'category', 'categories', 'entity', 'permission'));  
    }

    /**
     * 5. Actualizar categoría.
     */
    public function update(Request $request, Category $category){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/'.$request->entity)
                ->withErrors($validator)
                ->withInput();
        } 

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->subcategory = $request->category_father? $request->category_father:'0';
        $category->status = $request->status? 1:'0';
        $category->save();  

        $msg = trans('textos.categoria_updated');
        return redirect()->route('categories.edit', $category)->with(compact('msg')); 
    }

    /**
     * 6. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $category = Category::find($id);
            $category->status = $status;
            $category->save();

            $resp = ($category)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar categoría.
     */
    public function destroy(Category $category){
        //Devolvemos al módulo correcto:
        $module = Module::select('id', 'name', 'slug', 'label')->find($category->module_id);

        $route = $this->entities[$module->slug][0];

        $category->delete();

        $msg = trans('textos.categoria_deleted');
        return redirect()->route($route.'.index')->with(compact('msg'));       
    }
}

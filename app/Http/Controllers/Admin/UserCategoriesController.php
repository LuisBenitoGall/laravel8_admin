<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\User;
use App\Models\UserCategories;

class UserCategoriesController extends Controller{
    /**
     * 1. Categorías de usuario.
     * 1.1. Listado de categorías para DataTable.
     * 2. Formulario nueva categoría.
     * 3. Guardar nueva categoría.
     * 4. Editar categoría.
     * 5. Actualizar categoría.
     * 6. Actualizar estado de categoría.
     * 7. Eliminar categoría.
     */   
    
    private $option = 'usuarios';
    private $module = 'users';

    public function __construct(){
        $this->middleware('user_categories_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);
        }
    } 

    /**
     * 1. Empresas vinculadas al usuario.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'categorias',
            'module' => $this->module,
            'slug' => 'user-categories'
        ];

        return view('admin.user_categories.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de categorías para DataTable.
     */
    public function getUserCategoriesDT(){
        $categories = UserCategories::where('company_id', session('currentCompany'))
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('user-categories.edit');
        $eliminar = Auth::user()->can('user-categories.destroy');

        foreach($categories as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;
        }

        return datatables($categories)->toJson();
    }  

    /**
     * 2. Formulario nueva categoría.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.categoria_nueva',
            'module' => $this->module,
            'slug' => 'user-categories'
        ];

        return view('admin.user_categories.create')->with(compact('header'));   
    }

    /**
     * 3. Guardar nueva categoría.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/user-categories/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->input('status')? 1:0;

        $category = new UserCategories();
        $category->name = $request->input('name');
        $category->slug = $slug;
        $category->company_id = session('currentCompany');
        $category->status = $status;
        $category->save();

        $msg = trans('textos.categoria_creado_ok');
        return redirect()->route('user-categories.edit', $category->id)->with(compact('msg'));
    }

    /**
     * 4. Editar categoría.
     */
    public function edit(UserCategories $category){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.categoria_editar',
            'module' => $this->module,
            'slug' => 'user-categories'
        ];

        return view('admin.user_categories.edit')->with(compact('header', 'category')); 
    }

    /**
     * 5. Actualizar categoría.
     */
    public function update(Request $request, UserCategories $category){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/user-categories/'.$category->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->input('status')? 1:0;

        $category->name = $request->input('name');
        $category->slug = $slug;
        $category->status = $status;
        $category->save();

        $msg = trans('textos.categoria_updated');
        return redirect()->route('user-categories.edit', $category->id)->with(compact('msg'));
    }

    /**
     * 6. Actualizar estado de categoría.
     */
    public function status(Request $request, UserCategories $category, $status){
        if($request->ajax()){       
            $category->status = $status;
            $category->save();

            $resp = ($category)? true:false;
            return response()->json($resp);
        }
    }
    
    /**
     * 7. Eliminar categoría.
     *
     * @param  UserCategories $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserCategories $category){
        $category->delete();

        $msg = trans('textos.categoria_deleted');
        return redirect()->route('user-categories.index')->with(compact('msg'));    
    }
}

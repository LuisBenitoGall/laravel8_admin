<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Content;
use App\Models\ContentImage;

class ContentController extends Controller{
    /**
     * 1. Listado de contenidos.
     * 1.1. Listado de contenidos para DataTable.
     * 2. Formulario nuevo contenido.
     * 3. Guardar nuevo contenido.
     * 4. Generar .json de contenidos.
     * 5. Editar contenido.
     * 6. Actualizar contenido.
     * 7. Activar / desactivar contenido.
     * 8. Eliminar contenido.
     */
    
    private $option = 'configuracion';  
    private $module = 'settings';
    private $languages;

    public function __construct(){
        $this->languages = Helpers::languages();
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:150',
                'type' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:150',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5000'
            ]);    
        }
    }

    //0.1. Validar content code:
    protected function validateCode(array $arrCode){
        return Validator::make($arrCode, [
            'code' => 'unique:contents'
        ],
        [
            'unique' => trans('textos.contenido_nombre_existe')
        ]);    
    }

    /**
     * 1. Listado de contenidos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.contenidos'),
            'module' => $this->module,
            'slug' => 'contents'
        ];

        //Importación de contenidos desde .json. Se ejecuta 1 vez.
        Content::import();

        return view('admin.contents.index')->with(compact('header'));
    }   

    /**
     * 1.1. Listado de contenidos para DataTable.
     */
    public function getContentsDT(){ 
        $contents = Content::get();

        //Permisos del usuario:
        $editar = Auth::user()->can('contents.edit');
        $eliminar = Auth::user()->can('contents.destroy');

        foreach($contents as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;
        }

        return datatables($contents)->toJson();
    }

    /**
     * 2. Formulario nuevo contenido.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.contenido_nuevo'),
            'module' => $this->module,
            'slug' => 'contents'
        ];

        return view('admin.contents.create')->with(compact('header'));     
    }

    /**
     * 3. Guardar nuevo contenido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/contents/create')
                ->withErrors($validator)
                ->withInput();
        }  

        $code = Str::slug($request->name);
        $arrCode = ['code'=>$code];
        //Validamos el código:
        $validateCode = $this->validateCode($arrCode);
        if($validateCode->fails()){
            return redirect('admin/contents/create')
                ->withErrors($validateCode)
                ->withInput();
        }  

        $status = $request->input('status')? 1:0;

        $content = new Content;
        $content->name = $request->input('name');
        $content->code = $code;
        $content->type = $request->input('type');
        $content->user_id = Auth::user()->id;
        $content->status = $status;
        $content->save();

        //Generar .json de contenidos:
        $this->jsonContents();

        $msg = trans('textos.contenido_creado_ok');
        return redirect()->route('contents.edit', $content)->with(compact('msg'));
    }

    /**
     * 4. Generar .json de contenidos.
     *
     * @return .json
     */
    public function jsonContents(){
        $cont = Content::where('status', 1)->get();
        $jsondata = json_encode($cont);
        file_put_contents(storage_path()."/json/contents.json", $jsondata);

        $images = ContentImage::all();
        $jsonimages = json_encode($images);
        file_put_contents(storage_path()."/json/content-images.json", $jsondata);
    }

    /**
     * 5. Editar contenido.
     *
     * @param  \App\content  $content
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.contenido_editar'),
            'module' => $this->module,
            'slug' => 'contents'
        ];

        $languages = $this->languages;

        return view('admin.contents.edit')->with(compact('header', 'content', 'languages')); 
    }   

    /**
     * 6. Actualizar contenido.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\content $content
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/contents/'.$request->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $status = $request->input('status')? 1:0;
        $published_at = $request->input('published_at') != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->input('published_at')):$request->input('published_at'):NULL;
        $published_end = ($request->input('published_end') != '')? (session()->get('locale') != 'en')? Helpers::convertFecha($request->input('published_end')):$request->input('published_end'):NULL;

        //Desactivamos contenido si ha llegado fecha de fin de publicación:
        if($published_end != NULL && $published_end < date("Y-m-d H:i:s")) $status = 0;

        $links = explode(';', $request->input('links'));
        $links = serialize($links);

        $classes = [
            'css_titulo' => $request->input('css_titulo'),
            'css_destacado' => $request->input('css_destacado'),
            'css_contenido' => $request->input('css_contenido'),
            'css_links' => $request->input('css_links'),
            'css_imagenes' => $request->input('css_imagenes'),
            'css_video' => $request->input('css_video')
        ];
        $classes = serialize($classes);

        $arrTitles = [];
        $arrSlugs = [];
        $arrExcerpts = [];
        $arrTags = [];
        $arrContents = [];
        foreach($this->languages as $lang){
            $title = 'title_'.$lang[0];
            $excerpt = 'excerpt_'.$lang[0];
            $tags = 'tags_'.$lang[0];
            $content = 'content_'.$lang[0];

            $$tags = explode(';',$request->input($tags));

            $arrTitles[$lang[0]] = $request->input($title);
            $arrSlugs[$lang[0]] = Str::slug($request->input($title));
            $arrExcerpts[$lang[0]] = $request->input($excerpt);
            $arrTags[$lang[0]] = $$tags;
            $arrContents[$lang[0]] = $request->input($content);
        }

        $strTitles = serialize($arrTitles);
        $strSlugs = implode(';', $arrSlugs);
        $strExcerpts = serialize($arrExcerpts);
        $strTags = serialize($arrTags);
        $strContents = serialize($arrContents);

        $content = Content::find($request->input('id'));
        $content->name = $request->input('name');
        $content->title = $strTitles;
        $content->slug = $strSlugs;
        $content->excerpt = $strExcerpts;
        $content->content = $strContents;
        $content->tags = $strTags;
        $content->links = $links;
        $content->video = $request->input('video');
        $content->classes = $classes;
        $content->status = $status;
        $content->observations = $request->input('observations');
        $content->published_at = $published_at;
        $content->published_end = $published_end;
        $content->save();

        //Id del módulo contents:
        //$module = Module::select('id')->where('slug', 'contents')->first();

        //Guardamos categoría o subcategoría según exista ésta:
        //$category_id = $request->input('subcategory_id')? $request->input('subcategory_id'):$request->input('category_id');

        //Guardamos categorías si no existe previamente:
        /*$categoryExist = CategoryItem::select('id')
            ->where('item_id', $content->id)
            ->where('category_id', $request->input('category_id'))
            ->where('module_id', $module->id)
            ->first();
        if(!isset($categoryExist->id)){*/

        //Mientras que el contenido sólo tenga una categoría, se elimina la anterior y se genera nueva cada vez:
        
            /*CategoryItem::where('item_id', $content->id)
            ->where('module_id', $module->id)
            ->delete();

            $category = new CategoryItem;
            $category->item_id = $content->id;
            $category->category_id = $category_id;
            $category->module_id = $module->id;
            $category->save();*/
        //}

        //Generar .json de contenidos:
        $this->jsonContents();

        $msg = trans('textos.contenido_updated');
        return redirect()->route('contents.edit', $request->input('id'))->with(compact('msg'));
    }

    /*
     * 7. Activar / desactivar contenido.
     *
     * @param int $id @state
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $content = Content::find($id);
            $content->status = $status;
            $content->save();

            //Generar .json de contenidos:
            $this->jsonContents();

            $resp = ($status == 1)? trans('textos.contenido_on'):trans('textos.contenido_off');

            return response()->json($resp);
        }
    }

    /**
     * 8. Eliminar contenido.
     *
     * @param  str $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content){
        $content->delete();

        $msg = trans('textos.contenido_deleted');
        return redirect()->route('contents.index')->with(compact('msg'));    
    }  

}

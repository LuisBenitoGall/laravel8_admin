<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\User;
use App\Models\UserRecommendation;
use App\Models\UserRecommendationVote;

class UserRecommendationController extends Controller{
    /**
     * 1. Relación de recomendaciones de usuarios. 
     * 1.1. Relación de recomendaciones por usuario.
     * 1.2. Listado de recomendaciones para DataTable.
     * 2. Formulario nuevas recomendaciones.
     * 3. Guardar recomendaciones.
     * 4. Editar recomendación.
     * 5. Actualizar recomendación.
     * 6. Guardar imagen.
     * 7. Eliminar imagen.
     * 8. Mostrar recomendación.
     * 9. Empresas participantes.
     * 9.1. Listado de empresas participantes para DataTable.
     * 10. Vincular - desvincular empresa.
     * 11. Propiedades del iframe de video.
     * 12. Ranking.
     */
    
    private $option = 'recomendaciones';
    private $module = 'quizzes';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('user_recommendation_get', ['only' => ['edit']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:500'
            ]);
        }
    }

    /**
     * 1. Relación de recomendaciones de usuarios.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => '', 
            'module' => $this->module,
            'slug' => 'user-recommendations'
        ];

        $environment = 'all';

        return view('admin.user_recommendations.index', compact('header', 'environment'));
    }

    /**
     * 1.1. Relación de recomendaciones por usuario.
     */
    public function byUser(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.recomendaciones_mis', 
            'module' => $this->module,
            'slug' => 'user-recommendations'
        ];

        $environment = 'user';

        return view('admin.user_recommendations.index', compact('header', 'environment'));    
    }

    /**
     * 1.2. Listado de recomendaciones para DataTable.
     */
    public function getRecommendationsDT($environment){
        if($environment == 'all'){
            $data = UserRecommendation::select('users.name', 'users.surname', 'user_recommendations.id', 'user_recommendations.user_id', 'user_recommendations.published_at', 'user_recommendations.published_end')
            ->join('users', 'user_recommendations.user_id', '=', 'users.id')
            ->orderBy('user_recommendations.published_at', 'DESC');
        }elseif($environment == 'user'){
            $data = UserRecommendation::select('users.name', 'users.surname', 'user_recommendations.id', 'user_recommendations.user_id', 'user_recommendations.published_at', 'user_recommendations.published_end')
            ->join('users', 'user_recommendations.user_id', '=', 'users.id')
            ->where('user_recommendations.user_id', Auth::user()->id)
            ->orderBy('user_recommendations.published_at', 'DESC');    
        }

        return DataTables::of($data)
        ->addColumn('from', function($row){
            $from = $row->published_at? Carbon::parse($row->published_at)->format($this->languages[session()->get('locale')][4]):'';
            return $from; 
        })
        ->addColumn('to', function($row){
            $to = $row->published_end? Carbon::parse($row->published_end)->format($this->languages[session()->get('locale')][4]):'';
            return $to; 
        })
        ->addColumn('user', function($row){
            return $row->name.' '.$row->surname;
        })
        ->addColumn('acciones', function($row){
            return view('admin.user_recommendations.partials.dt-actions', compact('row'));
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevas recomendaciones.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.recomendacion_nueva', 
            'module' => $this->module
        ];

        return view('admin.user_recommendations.create', compact('header'));    
    }

    /**
     * 3. Guardar recomendaciones.
     */
    public function store(Request $request){
        if($request->song || $request->phrase || $request->image || $request->scene){

            $image = $this->storeImage($request);

            //Fecha de publicación:
            $last_published = UserRecommendation::select('published_end')
            ->where('status', 1)
            ->orderBy('published_end', 'DESC')
            ->first();

            $published_at = $last_published? Carbon::parse($last_published->published_end)->addDay()->format('Y-m-d 00:00:00'):Carbon::today()->addDay()->format('Y-m-d 00:00:00');
            $published_end = $last_published? Carbon::parse($last_published->published_end)->addDay()->format('Y-m-d 23:59:59'):Carbon::today()->addDay()->format('Y-m-d 23:59:59');

            //Modificando propiedades de los videos:
            $song = $request->song? $this->setVideoProperties($request->song):NULL;
            $scene = $request->scene? $this->setVideoProperties($request->scene):NULL;

            $ur = new UserRecommendation();
            $ur->user_id = Auth::user()->id;
            $ur->song = $song;
            $ur->phrase = $request->phrase;
            $ur->author = $request->author;
            $ur->image = $image;
            $ur->scene = $scene;
            $ur->published_at = $published_at;
            $ur->published_end = $published_end;
            $ur->status = 1;
            $ur->save();

            $msg = trans('textos.recomendacion_creada');
            return redirect()->route('user-recommendations.edit', $ur)->with(compact('msg'));
        }else{
            $alert = trans('textos.recomendacion_vacia');
            return redirect()->route('user-recommendations.create')->with(compact('alert'));    
        }
    }

    /**
     * 4. Editar recomendación.
     */
    public function edit(UserRecommendation $recommendation){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.recomendacion_editar', 
            'module' => $this->module
        ];

        $languages = $this->languages;

        return view('admin.user_recommendations.edit', compact('header', 'recommendation', 'languages'));
    }

    /**
     * 5. Actualizar recomendación.
     */
    public function update(Request $request, UserRecommendation $recommendation){
        $image = $this->storeImage($request);

        $recommendation->song = $request->song;
        $recommendation->phrase = $request->phrase;
        $recommendation->author = $request->author;
        if($image){
            $recommendation->image = $image;
        }
        $recommendation->scene = $request->scene;
        $recommendation->status = 1;
        $recommendation->save();

        $msg = trans('textos.recomendacion_creada');
        return redirect()->route('user-recommendations.edit', $recommendation)->with(compact('msg'));
    }

    /**
     * 6. Guardar imagen.
     */
    public function storeImage($request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/user-recommendations/create')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->hasFile('image')){
            $file = $request->file('image');
            $path = public_path().'/img/recommendations';
            $fileName = uniqid().$file->getClientOriginalName();
            $moved = $file->move($path, $fileName);

            //si el archivo ha sido guardado correctamente:
            if($moved){
               return $fileName;
            }else{
                return false;
            }
        }
    }

    /**
     * 7. Eliminar imagen.
     */
    public function deleteImage(UserRecommendation $recommendation){
        File::delete(public_path().'/img/recommendations/'.$recommendation->image);

        $recommendation->image = NULL;
        $recommendation->image_votes = NULL;
        $recommendation->save();

        $msg = trans('textos.imagen_deleted');  
        return redirect()->route('user-recommendations.edit', $recommendation)->with(compact('msg'));
    }

    /**
     * 8. Mostrar recomendación.
     */
    public function show(UserRecommendation $recommendation){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.recomendacion_editar', 
            'module' => $this->module,
            'slug' => 'user-recommendations'
        ];

        $user = User::select('name', 'surname')->find($recommendation->user_id);

        $votes = UserRecommendationVote::where('user_id', Auth::user()->id)
        ->where('recommendation_id', $recommendation->id)
        ->first();

        $languages = $this->languages;    

        return view('admin.user_recommendations.show', compact('header', 'recommendation', 'user', 'votes', 'languages'));
    }

    /**
     * 9. Empresas participantes.
     */
    public function companies(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.empresas_participantes', 
            'module' => $this->module,
            'slug' => 'user-recommendations'
        ];

        return view('admin.user_recommendations.companies', compact('header'));
    }

    /**
     * 9.1. Listado de empresas participantes para DataTable.
     */
    public function getCompaniesDT(){
        $data = Company::select('companies.id', 'companies.name', 'companies.tradename', 'company_settings.id AS cs_id', 'company_settings.recommendations')
        ->join('company_settings', 'companies.id', '=', 'company_settings.company_id')
        ->orderBy('companies.name', 'ASC');

        return DataTables::of($data)
        ->addColumn('acciones', function($row){
            return view('admin.user_recommendations.partials.dt-actions-companies', compact('row'));
        })
        ->make(true);
    }

    /**
     * 10. Vincular - desvincular empresa.
     */
    public function setCompany(Request $request, CompanySettings $cs, $status){
        if($request->ajax()){ 
            $cs->recommendations = $status;
            $cs->save();

            return response()->json(true);
        }
    }

    /**
     * 11. Propiedades del iframe de video.
     */
    private function setVideoProperties($video){
        //Eliminamos las propiedades de height y width:
        $pattern_width = '/width="(\d+)"/';
        if(preg_match($pattern_width, $video)){
            $video = preg_replace($pattern_width, '', $video);
        }

        $pattern_height = '/height="(\d+)"/';
        if(preg_match($pattern_height, $video)){
            $video = preg_replace($pattern_height, '', $video);
        }

        return $video;
    }

    /**
     * 12. Ranking.
     */
    public function ranking($tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.ranking', 
            'module' => $this->module,
            'slug' => 'user-recommendations'
        ];

        return view('admin.user_recommendations.ranking', compact('header', 'tab'));    
    }

    /**
     * 12.1. Listado de ranking por item.
     */
    public function getItemRankingDT($item){
        $field = $item.'_votes';

        $data = UserRecommendation::select('user_recommendations.*', 'users.name', 'users.surname', )
        ->join('users', 'user_recommendations.user_id', '=', 'users.id')
        ->where('user_recommendations.status', 1)
        ->whereNotNull('user_recommendations.'.$item)
        ->orderBy($field, 'DESC');

        return DataTables::of($data)
        ->addColumn('DT_RowIndex', function($row){
            return '';
        })
        ->addColumn('user', function($row){
            return $row->name.' '.$row->surname; 
        })
        ->addColumn('item', function($row) use($item){
            if($item == 'phrase'){
                if($row->phrase){
                    return '<i>'.$row->phrase.'</i><br><b>'.$row->author.'</b>';                    
                }
                    
            }elseif($item == 'song' || $item == 'scene'){
                return '<a href="#" class="btnViewRecommendation" data-toggle="modal" data-target="#modal-recommendation-item" data-type="video" data-item=\''.$row->$item.'\'><i class="fa fa-video"></i></a>';

            }elseif($item == 'image'){
                if($row->image){
                    return '<a href="#" class="btnViewRecommendation" data-toggle="modal" data-target="#modal-recommendation-item" data-type="image" data-item="'.$row->image.'"><i class="fa fa-image"></i></a>';
                }    
            }            
        })
        ->addColumn('date', function($row){
            $date = $row->published_at? Carbon::parse($row->published_at)->format($this->languages[session()->get('locale')][4]):'';
            return $date; 
        })
        ->addColumn('points', function($row) use($field){
            return $row->$field;
        })
        ->addColumn('acciones', function($row){
            return view('admin.user_recommendations.partials.dt-actions', compact('row'));
        })
        ->rawColumns(['DT_RowIndex', 'item'])
        //->make(true);
        ->toJson();
    }

}

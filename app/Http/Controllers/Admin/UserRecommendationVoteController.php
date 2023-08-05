<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

//Models:
use App\Models\UserRecommendation;
use App\Models\UserRecommendationVote;

class UserRecommendationVoteController extends Controller{
    /**
     * 1. Guardar voto.
     */
    
    private $option = 'recomendaciones';
    private $module = 'dashboard';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
    }

    /**
     * 1. Guardar voto.
     */
    public function store(Request $request){
        if($request->ajax()){
            $urv = UserRecommendationVote::where('user_id', Auth::user()->id)
            ->where('recommendation_id', $request->id)
            ->first();

            if(!$urv){
                $urv = new UserRecommendationVote();
                $urv->user_id = Auth::user()->id;
                $urv->recommendation_id = $request->id;
                $urv->save();
            }

            $recommendation = UserRecommendation::find($request->id);

            $votes = $request->like_ == 1? 1:NULL;

            if($request->item == 'song'){
                $urv->song = $votes;
                $recommendation->song_votes = $votes == 1? $recommendation->song_votes + 1:$recommendation->song_votes - 1;

            }elseif($request->item == 'phrase'){
                $urv->phrase = $votes;
                $recommendation->phrase_votes = $votes == 1? $recommendation->phrase_votes + 1:$recommendation->phrase_votes - 1;

            }elseif($request->item == 'scene'){
                $urv->scene = $votes;
                $recommendation->scene_votes = $votes == 1? $recommendation->scene_votes + 1:$recommendation->scene_votes - 1;

            }elseif($request->item == 'image'){
                $urv->image = $votes;
                $recommendation->image_votes = $votes == 1? $recommendation->image_votes + 1:$recommendation->image_votes - 1;
            }
            $urv->save();
            $recommendation->save();

            $response = $votes == 1? true:false;

            return response()->json($response);
        }
    }
}

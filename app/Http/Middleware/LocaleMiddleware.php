<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Carbon\Carbon;
use Session;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $languages = Helpers::languages();

        if(Session::has('locale') && array_key_exists(Session::get('locale'), $languages)){
            if(App::getLocale() != Session::get('locale')){
                App::setLocale(Session::get('locale'));
            }
        }else{
            Session::put('locale', 'es');
            App::setLocale('es');
        }

        return $next($request);
    }
}

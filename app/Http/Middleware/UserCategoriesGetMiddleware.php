<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\UserCategories;

class UserCategoriesGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $user_category_id = $request->segments()[2];

        //Si no existe la categoría:
        if(!UserCategories::where('id', $user_category_id)->exists()){
            $alert = trans('textos.categoria_no_existe');
            return redirect()->route('user-categories.index')->with(compact('alert'));   
            exit; 
        }

        $user_category = UserCategories::find($user_category_id);

        //Comprobamos que el almacén pertenezca a la empresa en sesión:
        if($user_category->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('user-categories.index')->with(compact('alert')); 
        }

        return $next($request);
    }
}

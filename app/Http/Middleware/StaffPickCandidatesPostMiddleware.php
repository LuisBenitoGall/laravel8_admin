<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\StaffPickCandidates;

class StaffPickCandidatesPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $company = StaffPickCandidates::select('staff_picks.company_id')
        ->join('staff_picks', 'staff_pick_candidates.staff_pick_id', '=', 'staff_picks.id')
        ->where('staff_pick_candidates.id', $request->id)
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('staff-pick.index')->with(compact('alert'));
            exit; 
        } 

        if($company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('staff-pick.index')->with(compact('alert'));
            exit; 
        }     

        return $next($request);
    }
}

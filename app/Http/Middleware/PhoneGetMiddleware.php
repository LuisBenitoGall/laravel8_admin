<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

// Models:
use App\Models\Phone;

class PhoneGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $phone_id = $request->segments()[2];

        $currentCompany = session('currentCompany');

        //Comprobamos que el teléfono pertenezca a la empresa o esté gestionado por otra empresa:
        $exist = Phone::select('phones.id', 'phones.workplace_id')
        ->join('workplaces', 'phones.workplace_id', '=', 'workplaces.id')
        ->leftJoin('company_accounts', 'workplaces.company_id', '=', 'company_accounts.company_id')
        ->where('phones.id', $phone_id)
        //->where('company_accounts.status', 1)
        ->where(function($q) use($currentCompany){
            $q->where('company_accounts.company_id', $currentCompany)
            ->orWhere('company_accounts.guardian', $currentCompany);
        })
        ->first();

        if(!$exist){
            return response()->json(false);    
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Module;
use App\Models\UserCompanies;

class LoginController extends Controller{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }

    /**
     * Set sessions
     * @return [type] [description]
     */
    protected function authenticated(){
        //Guardar .json de módulos opcionales:
        $this->jsonOptionalModules();

        //Empresas vinculadas al usuario:
        $companies = UserCompanies::userCompanies();

        //Empresa actual:
        if($companies->count() == 1){
            session(['currentCompany' => $companies[0]->id]); 

            //Módulos de la empresa:
            $companyModules = CompanyModules::getCompanyModules($companies[0]->id);

            foreach($companyModules as $row){
                \Session::push('modules', $row->module_id);
            }

            //Configuración de la empresa:
            $settings = CompanySettings::where('company_id', $companies[0]->id)->first();
            session(['companySettings' => $settings]);

        }elseif($companies->count() == 0){
            $this->logout();
        }

        //Empleo vigente del usuario:
        $employee = Employee::select('id')
        ->where('user_id', Auth::user()->id)
        ->where('status', 1)
        ->first();

        session(['userEmployee' => $employee]);
    }

    /**
     * Logout
     */
    public function logout(Request $request){
        //Borramos sesiones:
        $request->session()->forget(['currentCompany']);
 
        $request->session()->flush();
        
        $request->session()->invalidate();
        
        Auth::logout();
        return redirect('/');
    }
}

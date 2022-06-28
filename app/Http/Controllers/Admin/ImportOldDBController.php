<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\BudgetOrderSharedFields;
use App\Models\BusinessAreas;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CostCenters;
use App\Models\Country;
use App\Models\Importaciones;
use App\Models\ImportOldDB;
use App\Models\Province;
use App\Models\Town;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\Workplace;

class ImportOldDBController extends Controller{
    /**
     * 1. Vista de importación.
     * 1.1. Inicio de la importación.
     * 1.2. Mensajes sobre la importación.
     * 2. Importación usuarios.
     * 3. Importación empresas.
     * 4. Importación países.
     * 5. Importación provincias.
     * 6. Importar poblaciones.
     * 7. Importar centros de trabajo.
     * 8. Importar centros de coste.
     * 9. Importar áreas de negocio.
     * 10. Importar Usuarios por empresas.
     */
    
    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        //IVA:
        $this->co_iva = CompanySettings::co_iva();
    }

    /**
     * 1. Vista de importación.
     */
    public function index($activate = false){
        $header = [
            'option' => 'importacion', 
            'suboption' => 'textos.importacion',
            'module' => '',
            'slug' => ''
        ];  

        return view('admin.import_db.index')->with(compact('header', 'activate'));
    }

    /**
     * 1.1. Inicio de la importación.
     */
    public function start(Request $request){
        if($request->ajax()){  
            DB::table('importaciones')->truncate();
            
            $this->importUsers(true);
            sleep(3);
            $this->importCompanies();
            sleep(3);
            $this->importCountries();
            sleep(3);
            $this->importProvinces();
            sleep(3);
            $this->importTowns();
            sleep(3);
            $this->importWorkplaces();
            sleep(3);
            $this->importCostCenters();
            sleep(3);
            $this->importBusinessAreas();
            sleep(3);
            $this->importUserCompanies();
            sleep(3);
        } 
    }

    /**
     * 1.2. Mensajes sobre la importación.
     */
    public function messages($prev = false){
        $next = Importaciones::select('texto')
        ->orderBy('id', 'DESC')
        ->first();

        if($prev && $next->texto != $prev){
            return response()->json($next->texto);
        }else{
            return response()->json(false);    
        }
    }

    /**
     * 2. Importación usuarios.
     */
    public function importUsers($on = false){
        if($on){
            $data = DB::connection('mysql2')->table('users')->get();

            if($data->count()){
                Importaciones::create(['texto' => 'usuarios']);

                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table('users')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                foreach($data as $r){
                    $i = new User;
                    $i->id = $r->id;
                    $i->name = $r->name;
                    $i->surname = $r->surname;
                    $i->email = $r->email;
                    $i->email_verified_at = $r->email_verified_at;
                    $i->password = $r->password;
                    $i->nickname = $r->nickname;
                    $i->birthday = $r->birthday;
                    $i->nif = $r->nif;
                    $i->naf = $r->naf;
                    $i->signature = $r->signature;
                    $i->isAdmin = $r->isAdmin;
                    $i->status = $r->status;
                    $i->remember_token = $r->remember_token;
                    $i->deleted_at = $r->deleted_at;
                    $i->created_at = $r->created_at;
                    $i->updated_at = $r->updated_at;
                    $i->save();
                }
            }
        }
    }

    /**
     * 3. Importación empresas.
     */
    public function importCompanies(){
        $data = DB::connection('mysql2')->table('companies')->get();

        if($data->count()){
            Importaciones::create(['texto' => 'empresas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('companies')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach($data as $r){  
                $i = new Company;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->tradename = $r->tradename;
                $i->acronym = $r->acronym;
                $i->logo = $r->logo;
                $i->nif = $r->nif;
                $i->ccc = $r->ccc;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }  
    }

    /**
     * 4. Importación países.
     */
    public function importCountries(){
        $data = DB::connection('mysql2')->table('countries')->get();

        if($data->count()){
            Importaciones::create(['texto' => 'países']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('countries')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach($data as $r){  
                $i = new Country;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->code = $r->code;
                $i->alfa2 = $r->alfa2;
                $i->alfa3 = $r->alfa3;
                $i->flag = $r->flag;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }      
    }

    /**
     * 5. Importación provincias.
     */
    public function importProvinces(){
        $data = DB::connection('mysql2')->table('provinces')->get();

        if($data->count()){
            Importaciones::create(['texto' => 'provincias']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('provinces')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');  

            foreach($data as $r){  
                $i = new Province;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->country_id = $r->country_id;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 6. Importar poblaciones.
     * Omitir las poblaciones de España, pues se carga con el seeder.
     */
    public function importTowns(){
        // $data = DB::connection('mysql2')->table('towns')
        // ->select('towns.*')
        // ->join('provinces', 'towns.province_id', '=', 'provinces.id')
        // ->where('provinces.country_id', '!=', 1)
        // ->get();
        
        $data = DB::connection('mysql2')->table('towns')->get();

        if($data->count()){
            Importaciones::create(['texto' => 'poblaciones']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('towns')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');  

            foreach($data as $r){  
                $i = new Town;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->province_id = $r->province_id;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 7. Importar centros de trabajo.
     */
    public function importWorkplaces(){
        $data = DB::connection('mysql2')->table('workplaces')->get();

        if($data->count()){
            Importaciones::create(['texto' => 'centros de trabajo']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('workplaces')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');   

            foreach($data as $r){  
                $i = new Workplace;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->featured = $r->featured;
                $i->logo = $r->logo;
                $i->address = $r->address;
                $i->cp = $r->cp;
                $i->town_id = $r->town_id;
                $i->nif = $r->nif;
                $i->website = $r->website;
                $i->description = $r->description;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        } 
    }

    /**
     * 8. Importar centros de coste.
     */
    public function importCostCenters(){
        $data = DB::connection('mysql2')->table('cost_centers')->get();

        if($data->count()){
            Importaciones::create(['texto' => 'centros de coste']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('cost_centers')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');   

            foreach($data as $r){  
                $i = new CostCenters;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        } 
    }
    
    /**
     * 9. Importar áreas de negocio.
     */
    public function importBusinessAreas(){
        $data = DB::connection('mysql2')->table('business_areas')->get();

        if($data->count()){
            Importaciones::create(['texto' => 'áreas de negocio']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('business_areas')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');   

            foreach($data as $r){  
                $i = new BusinessAreas;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        } 
    }

    /**
     * 10. Importar Usuarios por empresas.
     */
    public function importUserCompanies(){
        $data = DB::connection('mysql2')->table('user_companies')->get();

        if($data->count()){
            Importaciones::create(['texto' => 'usuarios por empresas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_companies')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');   

            foreach($data as $r){  
                $i = new UserCompanies;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->company_id = $r->company_id;
                $i->categories = $r->categories;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }     
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;
use App\Helpers\Helpers;

//Models:
use App\Models\CompanyModules;
use App\Models\Content;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Functionality;
use App\Models\Invoice;
use App\Models\Module;
use App\Models\Order;
use App\Models\Project;
use App\Models\Province;
use App\Models\Town;
use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 1. Países.
     * 2. Provincias por país.
     * 3. Poblaciones por provincia.
     * 4. Generar .json de módulos.
     * 5. Generar .json de módulos opcionales.
     * 6. Generar .json de funcionalidades.
     * 7. Comprobar si empresa tiene módulo activo.
     * 8. Guardar firma de usuario.
     * 9. Session módulos por empresa.
     * 10. Subir logo.
     * 10.1. Subir archivo.
     * 11. Roles básicos.
     * 11.1. Roles por empresa.
     * 12. Importaciones iniciales.
     * 13. Generación número por patrón.
     *
     *
     * 5. Convertir fecha.
     *
     * 8. Obtener browser agent.
     * 9. Módulos asociados.
     * 13. Actualizar importe de factura por modificación en pedido.
     * 14. Sincronizar fechas de entrega y entrega prevista para pedidos y proyectos.
     */

    /**
     * 1. Países.
     *
     * @return \Illuminate\Http\Response
     */
    public function countries($status = ''){
        if($status == ''){
            $data = Country::select('name', 'id')->get();
        }else{
            $data = Country::select('name', 'id')->where('status', $status)->get();
        }

        //Si sólo hay un país, devolvemos sólo el id:
        $n = count($data);
        if($n == 1){
            return $data[0]->id;

        }else{
            $response = [];
            foreach($data as $dato){
                $response[$dato->id] = $dato->name;
            }
            return $response;
        }
    }

    /**
     * 2. Provincias por país.
     *
     * @param  [type]  $id    [description]
     * @param  string  $status [description]
     * @param  boolean $array [description]
     * @return [type]         [description]
     */
    public function countryProvinces($id, $status = '', $array = false){
        if($status == ''){
            $data = Province::select('name', 'id')->where('country_id', '=', $id)->get();
        }else{
            $data = Province::select('name', 'id')->where('country_id', '=', $id)->where('status', $status)->get();
        }

        //Convertir a array:
        if($array){
            $response = [];
            foreach($data as $dato){
                $response[$dato->id] = $dato->name;
            }
            return $response;

        }else{
            return $data;
        }
    }

    /**
     * 3. Poblaciones por provincia.
     *
     * @param  [type]  $id    [description]
     * @param  string  $status [description]
     * @param  boolean $array [description]
     * @return [type]         [description]
     */
    public function provinceTowns($id, $status = '', $array = false){
        if($status == ''){
            $data = Town::select('name', 'id')->where('province_id', '=', $id)->get();
        }else{
            $data = Town::select('name', 'id')->where('province_id', '=', $id)->where('status', $status)->get();
        }

        //Convertir a array:
        if($array){
            $response = [];
            foreach($data as $dato){
                $response[$dato->id] = $dato->name;
            }
            return $response;

        }else{
            return $data;
        }
    }

    /**
     * 4. Generar .json de módulos.
     *
     * @return .json
     */
    public function jsonModules(){
        $modules = Module::all();
        $jsondata = json_encode($modules);

        file_put_contents(storage_path()."/json/modules.json", $jsondata);
    }

    /**
     * 5. Generar .json de módulos opcionales.
     *
     * @return .json
     */
    public function jsonOptionalModules(){
        $modules = Module::where('level', 3)->get();

        //Agregamos funcionalidades de cada módulo:
        foreach($modules as $row){
            $functionalities = $row->functionalities;

            $row->setAttribute('functionalities', $functionalities);
        }

        $jsondata = json_encode($modules);
        file_put_contents(storage_path()."/json/secondary-menu.json", $jsondata);
    }

    /**
     * 6. Generar .json de funcionalidades.
     *
     * @return .json
     */
    public function jsonFunctionalities(){
        $functionalities = Functionality::all();
        $jsondata = json_encode($functionalities);

        file_put_contents(storage_path()."/json/functionalities.json", $jsondata);
    }

    /**
     * 7. Comprobar si empresa tiene módulo activo.
     */
    public function settedModule($module){
        $exist = false;

        if(session('modules')){
            $data = Module::where('slug', $module)->first();
            $exist = $data && in_array($data->id, session('modules'))? true:false;
        }
        return $exist;
    }

    /**
     * 8. Guardar firma de usuario.
     */
    public function loadUserSignature($request, $id){
        if($request->hasFile('signature')){
            $file = $request->file('signature');
            $path = public_path().'/img/signatures';
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
     * 9. Session módulos por empresa.
     */
    public function setCompanyModules($id){
        //Borramos módulos de la empresa anterior y obtenemos módulos de la empresa seleccionada.
        \Session::forget('modules');

        $modules = CompanyModules::getCompanyModules($id);

        foreach($modules as $row){
            \Session::push('modules', $row->module_id);
        }
    }

    /**
     * 10. Subir logo.
     */
    public function loadLogo($request, $folder){
        if($request->hasFile('logo')){
            $file = $request->file('logo');
            $path = public_path().'/img/'.$folder;
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
     * 10.1. Subir archivo.
     */
    public function loadFile($request, $folder){
        if($request->hasFile('doc')){
            $file = $request->file('doc');
            $path = public_path().'/docs/'.$folder;
            $fileName = uniqid().$file->getClientOriginalName();
            $moved = $file->move($path, $fileName);

            //si el archivo ha sido guardado correctamente:
            if($moved){
                return $fileName;
            }else{
                return NULL;
            }
        }else{
            return NULL;
        }
    }

    /**
     * 11. Roles básicos.
     */
    public function basicRoles(){
        return Role::where('name', '!=', config('constants.SUPER_ADMIN_'))
            ->whereNull('company_id')
            ->get();
    }

    /**
     * 11.1. Roles por empresa.
     */
    public function companyRoles($company_id){
        return Role::select('id', 'name')
            ->where('company_id', $company_id)
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * 12. Importaciones iniciales.
     *
     * Se realizan importaciones de datos maestros desde archivos .json. Esta acción sólo se ejecuta la primera vez de arrancar la aplicación.
     */
    public function initialImports(){
        //Monedas:
        Currency::import();

        //Contenidos:
        Content::import();

        //Unidades de venta:
        Unit::import();

        return true;
    }

    /**
     * 13. Generación número por patrón.
     */
    public function setPatternNumber($table, $pattern_id){
        $pattern = DB::table($table.'_patterns')->where('id', $pattern_id)->first();
        $arrPattern = unserialize($pattern->pattern);

        //Casos especiales:
        if($table == 'purchase'){
            $table = 'order';
            $change_again = 'purchase';
        }elseif($table == 'budget'){
            $table = 'order';
            $change_again = 'budget';
        }elseif($table == 'delivery'){
            $table = 'deliverie';
            $change_again = 'delivery';
        }else{
            $change_again = $table;
        }

        switch($change_again){
            case 'purchase':
                $cardinal_ = 'purchase_num_cardinal';
                $pattern_ = 'purchase_pattern_id';
                break;
            case 'budget':
                $cardinal_ = 'budget_num_cardinal';
                $pattern_ = 'budget_pattern_id';
                break;
            case 'order':
                $cardinal_ = 'order_num_cardinal';
                $pattern_ = 'order_pattern_id';
                break;
            default:
                $cardinal_ = 'num_cardinal';
                $pattern_ = 'pattern_id';
                break;
        }

        // $cardinal_ = $change_again == 'purchase'? 'purchase_num_cardinal':'num_cardinal';
        // $pattern_ = $change_again == 'purchase'? 'purchase_pattern_id':'pattern_id';

        if($pattern->yearly_reset == 1){
            $last_num = DB::table($table.'s')->select($change_again.'_num', $cardinal_, $pattern_)
            ->where($pattern_, $pattern->id)
            ->whereYear('created_at', date('Y'))
            ->orderBy($cardinal_, 'DESC')
            ->first();

        }else{
            $last_num = DB::table($table.'s')->select($change_again.'_num', $cardinal_, $pattern_)
            ->where($pattern_, $pattern->id)
            ->orderBy($cardinal_, 'DESC')
            ->first();
        }
        $num = $last_num && $last_num->$cardinal_? abs($last_num->$cardinal_)+1:1;

        $is_unique = false;
        $pattern_num = false;
        while(!$is_unique){
            //Nº de dígitos a completar:
            $arrDigits = str_split($num);
            $numDigits = count($arrDigits);

            //Buscamos el bloque de dígitos en el patrón:
            $bloque_digitos = false;
            foreach($arrPattern as $p){
                if(strpos($p, '|0') !== false){
                    $bloque_digitos = $p;
                    break;
                }
            }
            $digitos = trim($bloque_digitos, '|');
            $arrDigitos = str_split($digitos);
            $numDigitos = count($arrDigitos);

            $zeros = $numDigitos - $numDigits;
            $key = '';
            for($i=0; $i<$zeros; $i++){
                $key .= 0;
            }
            $key .= $num;

            foreach($arrPattern as $sgm){
                switch($sgm){
                    case '|year|':
                        $pattern_num .= date('Y');
                        break;
                    case $bloque_digitos:
                        $pattern_num .= $key;
                        break;
                    default:
                        $pattern_num .= $sgm;
                        break;
                }
            }

            //Comprobamos que el número creado no exista previamente:
            $exist = DB::table($table.'s')->where($change_again.'_num', $pattern_num)
            ->where($pattern_, $pattern_id)
            ->first();

            if(!$exist){
                $is_unique = true;
            }else{
                $num++;
                $pattern_num = false;
            }
        }

        $data = [
            'pattern_num' => $pattern_num,
            'num' => $num
        ];
        return $data;
    }

}

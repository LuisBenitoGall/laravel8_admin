<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use App\Notifications\PurchaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;
use PDF;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Order;
use App\Models\Sat;
use App\Models\SatItem;
use App\Models\SatPatterns;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\Workplace;

class SatController extends Controller{
    /**
     * 1. Servicios por empresa.
     * 1.1. Lista de servicios por empresa.
     * 2. Formulario nuevo servicio.
     * 3. Guardar nuevo servicio.
     * 4. Editar servicio.
     * 5. Actualizar servicio.
     * 6. Eliminar servicio.
     * 7. Actualizar estado de servicio.
     */
    
    private $option = 'servicios_asistencia_tecnica';
    private $module = 'sat';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:sat');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'type' => 'required_without:company_id',
                'company' => 'required_if:type,==,c|max:255',
                'nif' => 'required_without:company_id|max:20',
                'name' => 'required_if:type,==,u|max:150',
                'surname' => 'required_if:type,==,u|max:150',
                'email' => 'nullable|required_if:type,==,u|string|email|max:191|unique:users,email'
            ],[
                'type.required_without' => trans('textos.tipo_cliente_obligatorio'),
                'company.required_if' => trans('textos.empresa_obligatoria'),
                'name.required_if' => trans('textos.nombre_apellidos_obligatorios'),
                'surname.required_if' => trans('textos.nombre_apellidos_obligatorios'),
                'email.required_if' => trans('textos.email_obligatorio'),
                'email.unique' => trans('textos.email_existe'),
                'nif.required_without' => trans('textos.nif_obligatorio')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [


            ]);
        }
    }

    /**
     * 1. Servicios por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.servicios',
            'module' => $this->module,
            'slug' => 'sat'
        ];  

        return view('admin.sats.index')->with(compact('header'));   
    }

    /**
     * 1.1. Lista de servicios por empresa.
     */
    public function getSatsDT(){
        $data = Sat::select('sats.*', 'companies.name AS customer')
        ->join('companies AS companies', 'sats.contractor_id', '=', 'companies.id')
        ->where('sats.company_id', session('currentCompany'));

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $estados = helpers::satStatus();

        return DataTables::of($data)
        ->addColumn('num', function($row){
            $num = $row->sat_num? $row->sat_num:$row->id;
            return '<a href="'.route('sat.edit', $row).'">'.$num.'</a>';
        })
        ->addColumn('customer', function($row){
            return '<a target="_blank" href="'.route('customers.edit', $row->contractor_id).'">'.$row->customer.'</a>';
        })
        ->addColumn('provider', function($row){
            $provider = Order::select('companies.id', 'companies.name', 'companies.tradename')
            ->join('companies', 'orders.provider_id', '=', 'companies.id')
            ->where('orders.id', $row->purchase_id)
            ->first();

            if($provider){
                return '<a target="_blank" href="'.route('providers.edit', $provider->id).'">'.$provider->name.'</a>';
            }else{
                return '<span class="text-warning">'.ucfirst(trans('textos.sin_definir')).'</span>';
            }
        })
        ->addColumn('alta', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('delivery_date', function($row){
            if($row->delivery_date){
                return Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]);   
            }else{
                return '';
            }
        })
        ->addColumn('amount', function($row) use($currency){
            $order = Order::select('id')->find($row->sale_id);
            $amount = $order->getAmount();  
            return $amount['total_amount'].$currency->symbol;
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.sats.partials.dt-status', compact('row', 'estados'));    
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.sats.partials.dt-actions', compact('row', 'estados'));    
        })
        ->rawColumns(['num', 'customer', 'provider'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo servicio.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.sat_nuevo',
            'module' => $this->module,
            'slug' => 'sat'
        ];  

        return view('admin.sats.create')->with(compact('header'));     
    }

    /**
     * 3. Guardar nuevo servicio.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/sat/create')
                ->withErrors($validator)
                ->withInput();
        }

        $company_id = $request->company_id? $request->company_id:false;

        if(!$company_id){
            $company_name = $request->type == 'u'? $request->name.' '.$request->surname:$request->company;
            $slug = Str::slug($company_name);
            $is_user = $request->type == 'u'? true:false;

            //Creamos usuario:
            if($request->type == 'u'){
                $user = new User();
                $user->name = $request->name;
                $user->surname = $request->surname;
                $user->email = $request->email;
                $user->nif = $request->nif;
                $user->status = 1;
                $user->save();
            }    

            //Creamos empresa:
            $co = new Company();
            $co->name = $company_name;
            $co->slug = $slug;
            $co->tradename = $company_name;
            $co->nif = $request->nif;
            $co->is_user = $is_user;
            $co->save();

            $company_id = $co->id;

            //Centro de trabajo de la empresa:
            $wp = new Workplace();
            $wp->company_id = $company_id;
            $wp->name = ucfirst(trans('textos.sede')).' '.$co->name;
            $wp->slug = Str::slug(trans('textos.sede')).' '.$co->name;
            $wp->featured = 1;
            $wp->save();

            //Vinculando usuario con empresa:
            if($request->type == 'u'){
                $uc = new UserCompanies();
                $uc->user_id = $user->id;
                $uc->company_id = $co->id;
                $uc->save();
            }

            //Vinculando cliente-proveedor:
            $cp = new CustomerProviders();
            $cp->customer_id = $company_id;
            $cp->provider_id = session('currentCompany');
            $cp->workplace_id = $wp->id;
            $cp->save();
        }

        //Creamos el pedido de venta:
        $order = new Order();
        $order->is_order = true;
        $order->customer_id = $company_id;
        $order->provider_id = session('currentCompany');
        $order->created_by = session('currentCompany');
        $order->save();

        //Creando servicio:        
        $sat = new Sat();
        $sat->company_id = session('currentCompany');
        $sat->contractor_id = $company_id;
        $sat->sale_id = $order->id;
        $sat->user_id = Auth::user()->id;
        $sat->save();

        return redirect()->route('sat.edit', $sat);
    }

    /**
     * 4. Editar servicio.
     */
    public function edit(Sat $sat, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.sat_editar',
            'module' => $this->module,
            'slug' => 'sat'
        ];  

        //Pedido de venta:
        $order_sale = $sat->sale_id? Order::select('id', 'order_num', 'order_old_num', 'customer_id')->find($sat->sale_id):false;
        //Importe del pedido de venta:
        $sale_amount = $order_sale->getAmount();

        //Pedido de compra:
        $order_purchase = $sat->purchase_id? Order::select('id', 'order_num', 'order_old_num', 'provider_id')->find($sat->purchase_id):false;
        //Importe del pedido de compra:
        $purchase_amount = $order_purchase? $order_purchase->getAmount():'0';

        $customer = Company::select('id', 'name', 'tradename', 'nif', 'is_user')->find($sat->contractor_id);
        $provider = $order_purchase? Company::select('id', 'name', 'tradename', 'nif', 'is_user')->find($order_purchase->provider_id):false;

        //Items informativos:
        $items = SatItem::select('sat_items.*', 'users.name', 'users.surname')
        ->join('users', 'sat_items.user_id', '=', 'users.id')
        ->where('sat_items.sat_id', '=', $sat->id)
        ->orderBy('sat_items.created_at', 'DESC')
        ->get();

        //Patrones de numeración del SAT:
        $patterns = false;
        if(!$sat->pattern_id){
            $patterns = SatPatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        $estados = helpers::satStatus();

        $author = User::select('id', 'name', 'surname')->find($sat->user_id);

        $languages = $this->languages;

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return view('admin.sats.edit')->with(compact('header', 'sat', 'tab', 'order_sale', 'sale_amount', 'order_purchase', 'purchase_amount', 'customer', 'provider', 'items', 'patterns', 'estados', 'author', 'languages', 'currency'));    
    }

    /**
     * 5. Actualizar servicio.
     */
    public function update(Request $request, Sat $sat){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/sat/'.$sat->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Número de SAT:
        $sat_num = NULL;
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('sat', $request->pattern_id);
        }

        if($request->pattern_id){
            if(!empty($patternNum)){
                $sat->sat_num = $patternNum['pattern_num'];  
                $sat->sat_num_cardinal = $patternNum['num']; 
            }            
            $sat->sat_pattern_id = $request->pattern_id;
        }
        $sat->name = $request->name;
        $sat->description = $request->description;
        $sat->status = $request->status;
        $sat->save();

        $msg = trans('textos.servicio_updated');
        return redirect()->route('sat.edit', $sat)->with(compact('msg'));
    }

    /**
     * 6. Eliminar servicio.
     */
    public function destroy(Sat $sat){
        //Eliminamos los pedidos de venta y compra asociados.
        if($sat->sale_id){
            Order::where('id', $sat->sale_id)->delete();
        }
        if($sat->purchase_id){
            Order::where('id', $sat->purchase_id)->delete();
        }

        $sat->delete();

        $msg = trans('textos.servicio_deleted');
        return redirect()->route('sat.index')->with(compact('msg'));
    }

    /**
     * 7. Actualizar estado de servicio.
     */
    public function status(Request $request, Sat $sat, $status){
        if($request->ajax()){    
            $sat->status = $status;
            $sat->save();

            return response()->json(true);
        }
    }
    
}

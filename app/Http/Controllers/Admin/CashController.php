<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;
use PDF;
use Carbon\Carbon;

//Models:
use App\Models\Cash;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Currency;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Workplace;

class CashController extends Controller{
    /**
     * 1. Histórico de movimientos de caja.
     * 1.1. Movimientos de caja para DataTable.
     * 2. Formulario de movimiento de caja.
     * 3. Editar movimiento de caja.
     * 4. Actualizar movimiento de caja.
     * 5. Eliminar movimiento de caja.
     * 6. Importe de items.
     * 7. Actualizar cliente de la venta.
     * 8. Actualizar estado de movimiento de caja.
     * 9. Imprimir ticket de caja.
     */
    
    private $option = 'caja';
    private $module = 'cash';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:cash');
        $this->middleware('cash_get', ['only' => ['edit', 'update', 'destroy', 'print']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación update:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                'product_id' => 'nullable|integer',
                'qty' => 'nullable|numeric|min:0|not_in:0',
                'payment_method' => 'required|integer',
                'workplace' => 'required|integer'
            ]);
        }
    } 

    /**
     * 1. Histórico de movimientos de caja.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.caja_historico',
            'module' => $this->module,
            'slug' => 'cash'
        ]; 

        return view('admin.cash.index')->with(compact('header'));
    }

    /**
     * 1.1. Movimientos de caja para DataTable.
     */
    public function getCashDT(){
        $data = Cash::select('cashes.*', 'workplaces.name AS workplace', 'currencies.symbol', 'payment_methods.name AS pm', 'companies.name AS company', 'companies.tradename')
        ->join('workplaces', 'cashes.workplace_id', '=', 'workplaces.id')
        ->join('payment_methods', 'cashes.payment_method_id', '=', 'payment_methods.id')
        ->join('currencies', 'cashes.currency_id', '=', 'currencies.id')
        ->leftJoin('companies', 'cashes.customer_id', '=', 'companies.id')
        ->where('workplaces.company_id', session('currentCompany'))
        ->orderBy('cashes.created_at', 'DESC');

        $estados = helpers::cashStatus();

        return DataTables::of($data)
        ->addColumn('date', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]); 
        })
        ->addColumn('cash_center', function($row){
            return $row->workplace; 
        })
        ->addColumn('customer', function($row){
            $co = $row->tradename? $row->tradename:($row->company? $row->company:false);
            if($co){
                return '<a target="_blank" href="/admin/companies/'.$row->customer_id.'/show">'.$co.'</a>';
            }else{
                return '';
            }            
        })
        ->addColumn('amount', function($row){
            return $row->cash_amount.$row->symbol; 
        })
        ->addColumn('payment_method', function($row){
            return $row->pm; 
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.cash.partials.dt-status', compact('row', 'estados'));
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.cash.partials.dt-actions', compact('row', 'estados'));
        })
        ->rawColumns(['customer'])
        ->make(true);
    }

    /**
     * 2. Formulario de movimiento de caja.
     */
    public function create(){
        $workplace = Auth::user()->workplace;

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Generamos el movimiento de caja:
        $cash = new Cash();
        $cash->workplace_id = $workplace? $workplace->workplace_id:NULL;
        $cash->currency_id = $currency? $currency->id:NULL;
        $cash->created_by = Auth::user()->id;
        $cash->save();
        
        return redirect()->route('cash.edit', $cash);
    }

    /**
     * 3. Editar movimiento de caja.
     */
    public function edit(Cash $cash){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.caja_accion',
            'module' => $this->module,
            'slug' => 'cash-create'
        ]; 

        //Actualizando importe de caja:
        $amounts = $this->cashItemsAmount($cash->id);
        $cash->cash_amount = $amounts['total_amount']; 
        $cash->save();

        //Monedas:
        $currencies = Currency::where('status', 1)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        //Moneda:
        $currency = $cash->currency_id? Currency::find($cash->currency_id):false;

        //Métodos de pago:
        $payment_methods = PaymentMethod::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Centros de trabajo:
        $workplaces = Workplace::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Autor:
        $author = User::select('name', 'surname')->find($cash->created_by);

        //Cliente:
        $customer = $cash->customer_id? Company::select('id', 'name', 'tradename')->find($cash->customer_id):false;

        $items = Item::where('cash_id', $cash->id)->get();

        $languages = $this->languages;

        return view('admin.cash.edit')->with(compact('header', 'currencies', 'currency', 'cash', 'payment_methods', 'workplaces', 'author', 'customer', 'items', 'languages'));
    }

    /**
     * 4. Actualizar movimiento de caja.
     */
    public function update(Request $request, Cash $cash){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/cash/'.$cash->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->product_id){
            //Número de items:
            $n = Item::where('cash_id', $cash->id)->count('id');

            //Líneas:
            $i = new Item();
            $i->product_id = $request->product_id;
            $i->cash_id = $cash->id;
            $i->budget_sort = $n+1;
            $i->concept = $request->product_;
            $i->observations = $request->observations;
            $i->qty = $request->qty;
            $i->price = $request->price;
            $i->iva = $request->iva;
            $i->discount = $request->discount;
            $i->user_id = Auth::user()->id;
            $i->save();
        }

        $cash->workplace_id = $request->workplace;
        $cash->customer_id = $request->company_id;
        $cash->payment_method_id = $request->payment_method;
        $cash->currency_id = $request->currency;
        $cash->save();

        $msg = trans('textos.item_anadido');
        return redirect()->route('cash.edit', $cash)->with(compact('msg')); 
    }

    /**
     * 5. Eliminar movimiento de caja. 
     */
    public function destroy(Cash $cash){
        //Eliminar líneas:
        Item::where('cash_id', $cash->id)
        ->forceDelete();

        $cash->delete();

        $msg = trans('textos.caja_deleted');
        return redirect()->route('cash.index')->with(compact('msg')); 
    }

    /**
     * 6. Importe de items.
     */
    public function cashItemsAmount($cash_id){
        $items = Item::select('id', 'qty', 'price', 'iva', 'discount')
        ->where('cash_id', $cash_id)
        ->get();

        $total_base = 0;
        $total_amount = 0;  //Incluye IVA.

        foreach($items as $i){
            $amount = $i->qty > 0 && $i->price > 0? $i->qty * $i->price:'0';

            //Descuento:
            if($i->discount){
                if(helpers::isSerialized($i->discount)){
                    $discounts = unserialize($i->discount);
                }else{
                    $discounts = [$i->discount];
                }

                foreach($discounts as $d){
                    if($d > 0){
                        $discount_amount = helpers::percent($amount, $d);
                        $amount = $amount - $discount_amount;
                    }
                }
            }

            $total_base = $total_base + $amount;

            //IVA:
            if($i->iva > 0){
                $percent = ($amount * $i->iva) / 100;  
                $amount = $amount + $percent;  
            }

            $total_amount = $total_amount + $amount;
        }

        $data = [
            'total_base' => round($total_base, 2),
            'total_amount' => round($total_amount, 2)
        ];

        return $data;
    }

    /**
     * 7. Actualizar cliente de la venta.
     */
    public function setCustomer(Cash $cash, $company_id){
        $cash->customer_id = $company_id;
        $cash->save();

        $msg = trans('textos.cliente_creado_ok');
        return redirect()->route('cash.edit', $cash)->with(compact('msg'));
    }

    /**
     * 8. Actualizar estado de movimiento de caja.
     */
    public function status(Request $request, Cash $cash, $status){
        if($request->ajax()){ 
            $cash->status = $status;
            $cash->save();

            return response()->json(true);
        }    
    }

    /**
     * 9. Imprimir ticket de caja.
     */
    public function print(Cash $cash){
        //Cliente:
        $customer = $cash->customer_id? Company::select('id', 'name', 'tradename')->find($cash->customer_id):false;


        //IMPLEMENTAR SCRIPTS PARA IMPRESION POR IMPRESORA TÉRMICA:
        //Tutorial: https://parzibyte.me/blog/2020/03/04/laravel-imprimir-ticket-impresora-termica/
    }
}

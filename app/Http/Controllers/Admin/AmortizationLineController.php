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
use Carbon\Carbon;

//Models:
use App\Models\Amortization;
use App\Models\AmortizationLine;
use App\Models\CompanySettings;

class AmortizationLinesController extends Controller{
    /**
     * 1. Guardar nueva línea.
     * 2. Actualizar línea.
     * 3. Eliminar línea de amortización.
     * 4. Actualizar importe pendiente de amortizar.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;
    private $currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->currency = CompanySettings::co_currency(0);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])) {
            return Validator::make($data, [
                'amortization_id' => 'required|integer',
                'amount' => 'required',
                'percent' => 'required'
            ]);

        //Validación update:
        } elseif (isset($data['submit_update'])) {
            return Validator::make($data, [
                'amortization_id' => 'required|integer',
                'id' => 'required|integer',
                'amount' => 'required',
                'percent' => 'required'
            ]);
        }
    }

    /**
     * 1. Guardar nueva línea.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $validator->getMessageBag()->add('accion', 'modal-add-line');

            return redirect('/admin/amortizations/'.$request->amortization_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $al = new AmortizationLine();
        $al->amortization_id = $request->amortization_id;
        $al->amount = $request->amount;
        $al->percent = $request->percent;
        $al->save();

        //Actualizando importe pendiente:
        $this->updPendentAmount($request->amortization_id);

        $msg = trans('textos.amortizacion_linea_creada');
        return redirect()->route('amortizations.edit', [$request->amortization_id, 'items'])->with(compact('msg'));
    }

    /**
     * 2. Actualizar línea.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $validator->getMessageBag()->add('accion', 'modal-edit-line');

            return redirect('/admin/amortizations/'.$request->amortization_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        AmortizationLine::where('id', $request->id)
        ->update(['amount' => $request->amount, 'percent' => $request->percent]);

        //Actualizando importe pendiente:
        $this->updPendentAmount($request->amortization_id);

        $msg = trans('textos.amortizacion_linea_updated');
        return redirect()->route('amortizations.edit', [$request->amortization_id, 'items'])->with(compact('msg'));
    }

    /**
     * 3. Eliminar línea de amortización.
     */
    public function destroy(AmortizationLine $line){
        $amortization_id = $line->amortization_id;

        $line->delete();

        //Actualizando importe pendiente:
        $this->updPendentAmount($amortization_id);

        $msg = trans('textos.amortizacion_linea_deleted');
        return redirect()->route('amortizations.edit', [$amortization_id, 'items'])->with(compact('msg'));
    }

    /**
     * 4. Actualizar importe pendiente de amortizar.
     */
    public function updPendentAmount($amortization_id){
        $amortization = Amortization::select('id', 'amount', 'amortization_pendent')->find($amortization_id);
        $amortization->updatePendentAmount();
    }
}

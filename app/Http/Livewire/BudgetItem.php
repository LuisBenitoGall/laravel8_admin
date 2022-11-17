<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;

//Models:
use App\Models\BudgetFormItems;
use App\Models\BudgetForms;
use App\Models\CompanySettings;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Order;

class BudgetItem extends Component{
    /**
     * 1. Render componente.
     * 2. Guardar registro.
     * 3. Añadir item desde bloque.
     * 4. Select 2.
     * 5. Editar item.
     * 6. Reset formulario.
     * 7. Ordenar item hacia arriba.
     * 7.1. Ordenar item hacia abajo.
     */

    public $order;

    public $action;
    public $add_items;
    public $amount_by_blocks;
    public $amount_without_percentage_items;
    public $block;
    public $blocks;
    public $calculated_by_percent;
    public $concept;
    public $co_iva;
    public $currency;
    public $days;
    public $days_continue;
    public $discount;
    public $field_readonly;
    public $form_on;
    public $item;
    public $items_order;
    public $items_no_price;
    public $items_selected;
    public $observations;
    public $price;
    public $print;
    public $qty;
    public $total_amount;
    public $sub_total;
    public $rowId;
    public $tempSubToralRow;

    protected $rules = [
        'block' => 'required|integer',
        'concept' => 'required',
        'price' => 'required',
        'qty' => 'required'
    ];

    public function mount($order){
        $this->blocks = BudgetFormItems::getBudgetBlocks($order->form_id, $order->id);

        $this->amount_by_blocks = [];
        $this->tempSubToralRow = 0;
        $this->rowId = 0;
        $this->sub_total = [];
        $this->co_iva = CompanySettings::co_iva();
        $this->field_readonly = false;
        $this->order = $order;

        $this->item = false;
        $this->items_order = [];
        $this->items_selected = [];

        //Bloqueo de factura. Se pueden agregar o eliminar items de un presupuesto si la factura o facturas no están todas bloqueadas.
        $this->add_items = false;
        $invoices = Invoice::select('blocked')->where('order_id', $order->id)->get();
        if($invoices->count()){
            foreach ($invoices as $inv) {
                if ($inv->blocked == 0) {
                    $this->add_items = true;
                }
            }
        } else {
            $this->add_items = true;
        }

        $this->form_on = false;

        $this->print = true;

        $this->items_no_price = helpers::statusBudgetLinesWithoutPrice();
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        //Moneda:
        $this->currency = CompanySettings::co_currency(0);

        //Items del presupuesto:
        $this->items_selected = Item::where('order_id', $this->order->id)
            ->orderBy('budget_form_block_id', 'ASC')
            ->orderByRaw('ISNULL(budget_order), budget_order ASC')
            ->get();
        //->orderBy('budget_order', 'ASC')

        //Importe total:
        $arr_amounts = $this->order->getAmount();
        $this->total_amount = $arr_amounts['amount_with_discount'];

        //Importe sin items porcentuales:
        $this->amount_without_percentage_items = $this->order->itemsBudgetOrderAmount(false);

        //Importe por bloques:
        if (count($this->items_selected)) {
            foreach ($this->blocks as $block) {
                //Número de items por bloque:
                $this->items_order[$block->id] = 0;

                $block_amount = 0;
                foreach ($this->items_selected as $is) {
                    if ($block->id == $is->budget_form_block_id) {
                        // $this->rowId = $is->id;
                        //Items en el bloque:
                        $this->items_order[$block->id] = $this->items_order[$block->id] + 1;

                        $arr_subtotal = helpers::itemAmount($is);
                        $subtotal = $arr_subtotal['amount'];
                        if($is->calculated_by_percent){
                            $subtotal = helpers::itemAmountPercentage($is->calculated_by_percent, $this->amount_without_percentage_items);   
                        }

                        $block_amount = $block_amount + $subtotal;
                        if (isset($this->item)) {
                            $this->sub_total[$is->id ?? 0] = $subtotal;
                        }

                    }
                }
                $this->amount_by_blocks[$block->id] = $block_amount;
            }
        }


        return view('livewire.budget-item');
    }

    /**
     * 2. Guardar registro.
     */
    public function store(){
        $this->validate();

        if ($this->concept) {
            //Sin precio:
            if (is_numeric($this->price)) {
                $price = $this->price;
                $no_price = NULL;
            } else {
                $price = NULL;

                //Recuperamos el índice de la opción seleccionada:
                $idx = array_search(trans('textos.' . $this->price), $this->items_no_price);

                $no_price = $idx;
            }

            //Precio porcentual:
            if ($this->calculated_by_percent) {
                $price = $this->calculated_by_percent;
            }

            //Descuento por jornadas continuas. El valor de days_continue_discount depende de que days_continue == 1, y del valor que tengan asignado el campo labor_journeys de la tabla budget_forms:
            $days_continue_discount = NULL;
            if ($this->days_continue) {
                $labor_journeys = BudgetForms::select('budget_forms.labor_journeys')
                    ->join('orders', 'budget_forms.id', '=', 'orders.form_id')
                    ->where('orders.id', $this->order->id)
                    ->first();

                if ($labor_journeys && $this->days > 1) {
                    $arr_labor_journeys = $labor_journeys->labor_journeys ? unserialize($labor_journeys->labor_journeys) : [];

                    //Comprobamos si existe el índice que viene dado por el número de días ($days):
                    if (array_key_exists($this->days, $arr_labor_journeys)) {
                        $days_continue_discount = $arr_labor_journeys[$this->days];

                        //Si no existe comprobamos los operadores y buscamos el índice de labor_journeys más próximo al número de días indicado. Obtenemos primero los índices del objeto:
                    } else {
                        $indices = [];
                        foreach ($arr_labor_journeys as $idx => $value) {
                            array_push($indices, $idx);
                        }

                        end($indices);
                        $last_indice = key($indices);
                        foreach ($indices as $indx) {
                            if (($this->days - $indx) < $last_indice) {
                                $last_indice = $indx;
                            }
                        }
                        $days_continue_discount = $arr_labor_journeys[$last_indice];
                    }
                }
                $days_continue_discount = $days_continue_discount != NULL && is_array($days_continue_discount) ? $days_continue_discount['discount'] : NULL;
            }

            $item = false;

            //Cantidad mínima en caso de no venir declarada.
            $qty = $this->qty == 0 || $this->qty == '' ? 1 : $this->qty;

            //Update:
            if ($this->action == 'update') {
                $item = Item::find($this->item['id']);
                $item->budget_form_block_id = $this->block;
                $item->concept = $this->concept;
                $item->observations = $this->observations;
                $item->qty = $qty;
                $item->price = $price;
                $item->item_no_price = $no_price;
                $item->discount = $this->discount;
                $item->days = $this->days ? $this->days : 0;
                $item->days_continue = $this->days_continue ? true : false;
                $item->days_continue_discount = $days_continue_discount;
                $item->print = $this->print ? true : false;
                $item->save();

                //Insert:
            } elseif ($this->action == 'save') {
                $item = new Item();
                $item->product_id = $this->item ? $this->item['product_id'] : NULL;
                $item->order_id = $this->order->id;
                $item->budget_form_block_id = $this->block;
                $item->budget_form_item_id = $this->item ? $this->item['id'] : NULL;
                $item->budget_order = isset($this->items_order[$this->block]) ? $this->items_order[$this->block] + 1 : 1;
                $item->concept = $this->concept;
                $item->observations = $this->observations;
                $item->qty = $qty;
                $item->price = $price;
                $item->item_no_price = $no_price;
                $item->iva = $this->item ? $this->item['iva'] : $this->co_iva;
                $item->discount = $this->discount;
                $item->calculated_by_percent = $this->calculated_by_percent > 0 ? $this->calculated_by_percent : NULL;
                $item->days = $this->days ? $this->days : 0;
                $item->days_continue = $this->days_continue ? true : false;
                $item->days_continue_discount = $days_continue_discount;
                $item->print = $this->print ? true : false;
                $item->user_id = Auth::user()->id;
                $item->save();

                $this->item = $item;

                //Modificamos la acción por si se edita tras guardar:
                $this->action = 'update';
            }


            //Importe del presupuesto:
            $arr_amounts = $this->order->getAmount();

            //Enviando importe al componente budget-amount:
            $this->emit('updateAmount', $arr_amounts['amount_with_discount'], $arr_amounts['amount_without_discount']);
            $this->emit('updateAmountMini', $arr_amounts['amount_with_discount']);


            //Estado del formulario:
            $this->form_on = false;

            //refrescamos el componente
            $this->dispatchBrowserEvent('refresh');
        }
    }

    /**
     * 3. Añadir item desde bloque.
     */
    public function addItem($item){
        $this->resetForm();

        //Acción del formulario:
        $this->action = 'save';

        $this->item = $item;
        $this->block = $item['recid'];
        $this->concept = $item['name'];
        $this->observations = '';
        $this->print = true;

        //Comprobamos si el item tiene precio neto o porcentual:
        if ($item['calculated_by_percent'] > 0) {
            $this->field_readonly = true;

            $this->calculated_by_percent = $item['calculated_by_percent'];
            $this->qty = 1;
            $this->price = $item['calculated_by_percent'];
            $this->discount = '';

        } else {
            $this->field_readonly = false;

            $this->calculated_by_percent = false;
            $this->qty = $item['qty_min'] > 0 ? $item['qty_min'] : 1;
            $this->price = $item['priceForCustomer'] ? $item['priceForCustomer'] : $item['price'];
            $this->discount = $item['discount'];

        }

        $this->dispatchBrowserEvent('observations-value', ['observations' => '']);

        //Estado del formulario:
        $this->form_on = true;
    }

    /**
     * 4. Select 2.
     */
    public function hydrate(){
        $this->emit('select2_lw');
    }

    /**
     * 5. Editar item.
     */
    public function editItem($item){
        //Acción del formulario:
        $this->action = 'update';
        $this->rowId = $item;

        $this->item = $item;
        $this->block = $item['budget_form_block_id'];
        $this->concept = $item['concept'];
        $this->observations = $item['observations'];
        $this->print = $item['print'] == 1 ? true : false;

        //Comprobamos si el item tiene precio neto o porcentual:
        if ($item['calculated_by_percent'] > 0) {
            $this->field_readonly = true;

            $this->calculated_by_percent = $item['calculated_by_percent'];
            $this->qty = 1;
            $this->price = $item['calculated_by_percent'];
            $this->discount = '';
        } else {
            $this->field_readonly = false;

            $this->calculated_by_percent = false;
            $this->days = $item['days'];
            $this->days_continue = $item['days_continue'];
            $this->discount = $item['discount'];
            $this->price = $item['item_no_price'] ? $this->items_no_price[$item['item_no_price']] : $item['price'];
            $this->qty = $item['qty'];
        }

        //Estado del formulario:
        $this->form_on = true;

        $this->dispatchBrowserEvent('observations-value', ['observations' => $item['observations']]);
    }

    /**
     * 6. Reset formulario.
     */
    public function resetForm()
    {
        $this->item = false;
        $this->action = 'save';
        $this->field_readonly = false;
        $this->print = true;

        $this->reset(['concept', 'block', 'discount', 'price', 'qty', 'days', 'days_continue', 'observations']);
    }

    /**
     * 7. Ordenar item hacia arriba.
     */
    public function itemUp($item)
    {
        if ($item) {
            $new_order = $item['budget_order'];

            //Orden del elemento anterior:
            $prev = Item::select('id', 'budget_order')
                ->where('order_id', $item['order_id'])
                ->where('budget_form_block_id', $item['budget_form_block_id'])
                ->where('budget_order', '<', $item['budget_order'])
                ->orderBy('budget_order', 'DESC')
                ->first();

            if ($prev) {
                //Orden del elemento seleccionado:
                Item::where('id', $item['id'])
                    ->update(['budget_order' => $prev->budget_order]);

                $prev->budget_order = $new_order;
                $prev->save();

                $this->render();
            }
        }
    }

    /**
     * 7.1. Ordenar item hacia abajo.
     */
    public function itemDown($item)
    {
        if ($item) {
            $new_order = $item['budget_order'];

            //Orden del elemento siguiente:
            $next = Item::select('id', 'budget_order')
                ->where('order_id', $item['order_id'])
                ->where('budget_form_block_id', $item['budget_form_block_id'])
                ->where('budget_order', '>', $item['budget_order'])
                ->orderBy('budget_order', 'ASC')
                ->first();

            //Orden del elemento seleccionado:
            if ($next) {
                Item::where('id', $item['id'])
                    ->update(['budget_order' => $next->budget_order]);

                $next->budget_order = $new_order;
                $next->save();

                $this->render();
            }
        }
    }


    /**
     * get change Qty value for subtotal
     * @param $qty
     * @return void
     */
    public function changeQty($qty)
    {
        if (is_numeric($qty)) {
            if (!$this->days) {
                $subtotal = $qty * $this->price;
            } else {
                $subtotal = $qty * $this->price * $this->days;
            }

            if (!$this->discount) {
                $this->tempSubToralRow = $subtotal;
            } else {
                $discount = $this->discount * $subtotal / 100;
                $this->tempSubToralRow = $subtotal - $discount;
            }
        }
    }


    /**
     * get change price value for subtotal
     * @param $price
     * @return void
     */
    public function changePrice($price)
    {
        if (is_numeric($price)) {


            if (!$this->days) {
                $subtotal = $this->qty * $price;
            } else {
                $subtotal = $this->qty * $price * $this->days;
            }
            if (!$this->discount) {
                $this->tempSubToralRow = $subtotal;
            } else {
                $discount = $this->discount * $subtotal / 100;
                $this->tempSubToralRow = $subtotal - $discount;

            }
        }
    }


    /**
     * get change price value for subtotal
     * @param $discount
     * @return void
     */
    public function changeDiscount($discount)
    {

        if (is_numeric($discount)) {
            if (!$this->days) {
                $subtotal = $this->qty * $this->price;
            } else {
                $subtotal = $this->qty * $this->price * $this->days;
            }

            if (is_numeric($discount)) {
                $discount = $discount * $subtotal / 100;
                $this->tempSubToralRow = $subtotal - $discount;
            } else {
                $this->tempSubToralRow = $subtotal;
            }

        }
    }

    /**
     * get change Days value for subtotal
     * @param $days
     * @return void
     */
    public function changeDays($days)
    {

        if (is_numeric($days)) {
            if (!$days) {
                $subtotal = $this->qty * $this->price;
            } else {
                $subtotal = $this->qty * $this->price * $days;
            }

            if (!$this->discount) {
                $this->tempSubToralRow = $subtotal;
            } else {
                $discount = $this->discount * $subtotal / 100;
                $this->tempSubToralRow = $subtotal - $discount;

            }
        }

    }
}

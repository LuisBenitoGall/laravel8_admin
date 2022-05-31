<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Auth;

//Models:
use App\Models\Budget;
use App\Models\BudgetFormItems;
use App\Models\BudgetOrderSharedFields;
use App\Models\CompanySettings;
use App\Models\Item;

class BudgetItem extends Component{
    /**
     * 1. Render componente.
     * 2. Guardar registro.
     * 3. Añadir item desde bloque.
     * 4. Select 2.
     * 5. Editar item.
     * 6. Reset formulario.
     * 7. Ordenar item hacia arriba.
     * 7.1. Ordenar itema hacia abajo.
     */
    
    public $budget, $order, $shared;

    public $action;
    public $amount_by_blocks;
    public $amount_without_percentage_items;
    public $block;
    public $blocks;
    public $calculated_by_percent;
    public $concept;
    public $co_iva;
    public $currency;
    public $days;
    public $discount;
    public $field_readonly;
    public $form_on;
    public $item;
    public $items_order;
    public $items_selected;
    public $observations;
    public $price;
    public $print;
    public $qty;
    public $total_amount;

    protected $rules = [
        'block' => 'required|integer',
        'concept' => 'required',
        'price' => 'required',
        'qty' => 'required'
    ];

    public function mount($budget, $order, $shared){
        $this->blocks = BudgetFormItems::getBudgetBlocks($budget->form_id, $budget->id);

        $this->amount_by_blocks = [];
        $this->budget = $budget;
        $this->co_iva = CompanySettings::co_iva();
        $this->field_readonly = false;
        $this->order = $order;
        $this->shared = $shared;

        $this->item = false;
        $this->items_order = [];
        $this->items_selected = [];

        $this->form_on = false;
    }
    
    /**
     * 1. Render componente.
     */
    public function render(){
        //Moneda:
        $this->currency = CompanySettings::co_currency(0);

        //Items del presupuesto:
        $this->items_selected = Item::where('budget_order_id', $this->shared->id)
        ->orderBy('budget_form_block_id', 'ASC')
        ->orderBy('budget_order', 'ASC')
        ->get();

        //Importe total:
        $arr_amounts = $this->shared->getAmount();
        $this->total_amount = $arr_amounts['amount_with_discount'];

        //Importe sin items porcentuales:
        $this->amount_without_percentage_items = $this->shared->itemsBudgetOrderAmount(false);

        //Importe por bloques:
        if(count($this->items_selected)){
            foreach($this->blocks as $block){
                //Número de items por bloque:
                $this->items_order[$block->id] = 0;

                $block_amount = 0;
                foreach($this->items_selected as $is){
                    if($block->id == $is->budget_form_block_id){
                        //Items en el bloque:
                        $this->items_order[$block->id] = $this->items_order[$block->id] + 1;

                        $subtotal = $is->calculated_by_percent? Helpers::itemAmountPercentage($is->calculated_by_percent, $amount_without_percentage_items):Helpers::itemAmount($is);

                        $block_amount = $block_amount + $subtotal;
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

        if($this->concept){
            //Sin precio:
            if(is_numeric($this->price)){
                $price = $this->price;
                $no_price = NULL;
            }else{
                $price = NULL;
                $no_price = $this->price;
            }

            //Precio porcentual:
            if($this->calculated_by_percent){
                $price = NULL;
            }

            //Update:
            if($this->action == 'update'){
                $item = Item::find($this->item['id']);
                $item->budget_form_block_id = $this->block;
                $item->concept = $this->concept;
                $item->observations = $this->observations;
                $item->qty = $this->qty;
                $item->price = $price;
                $item->item_no_price = $no_price;
                $item->discount = $this->discount;
                $item->days = $this->days? $this->days:0;
                $item->print = $this->print? true:false;
                $item->save();
            
            //Insert:
            }elseif($this->action == 'save'){
                $item = new Item();
                $item->product_id = $this->item? $this->item['product_id']:NULL;
                $item->budget_order_id = $this->shared->id;
                $item->budget_form_block_id = $this->block;
                $item->budget_form_item_id = $this->item? $this->item['id']:NULL;
                $item->budget_order = isset($this->items_order[$this->block])? $this->items_order[$this->block] + 1:1;
                $item->concept = $this->concept;
                $item->observations = $this->observations;
                $item->qty = $this->qty;
                $item->price = $price;
                $item->item_no_price = $no_price;
                $item->iva = $this->item? $this->item['iva']:$this->co_iva;
                $item->discount = $this->discount;
                $item->calculated_by_percent = $this->calculated_by_percent > 0? $this->calculated_by_percent:NULL;
                $item->days = $this->days? $this->days:0;
                $item->print = $this->print? true:false;
                $item->user_id = Auth::user()->id;
                $item->save();

                $this->item = $item;

                //Modificamos la acción por si se edita tras guardar:
                $this->action = 'update';
            }            

            //Importe del presupuesto:
            $arr_amounts = $this->shared->getAmount();
            $budget_amount = number_format($arr_amounts['amount_with_discount'],2);

            //Importe sin descuento:
            $budget_amount_without_discount = number_format($arr_amounts['amount_without_discount'],2);    

            //Enviando importe al componente budget-amount:
            $this->emit('updateAmount', $budget_amount, $budget_amount_without_discount);
            $this->emit('updateAmountMini', $budget_amount);
        }
    }

    /**
     * 3. Añadir item desde bloque.
     */
    public function addItem($item){
        //Acción del formulario:
        $this->action = 'save';

        $this->item = $item;
        $this->block = $item['recid'];
        $this->concept = $item['name'];
        $this->print = true;

        //Comprobamos si el item tiene precio neto o porcentual:
        if($item['calculated_by_percent'] > 0){
            $this->field_readonly = true;

            $this->calculated_by_percent = $item['calculated_by_percent'];
            $this->qty = 1;
            $this->price = $item['calculated_by_percent'];
            $this->discount = '';

        }else{
            $this->field_readonly = false;

            $this->calculated_by_percent = false;
            $this->qty = $item['qty_min'] > 0? $item['qty_min']:1;
            $this->price = $item['price'];
            $this->discount = $item['discount'];
        }

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
        
        $this->item = $item;
        $this->block = $item['budget_form_block_id'];
        $this->concept = $item['concept'];
        $this->print = $item['print'] == 1? true:false;

        //Comprobamos si el item tiene precio neto o porcentual:
        if($item['calculated_by_percent'] > 0){
            $this->field_readonly = true;

            $this->calculated_by_percent = $item['calculated_by_percent'];
            $this->qty = 1;
            $this->price = $item['calculated_by_percent'];
            $this->discount = '';
        }else{
            $this->field_readonly = false;

            $this->calculated_by_percent = false;
            $this->days = $item['days'];
            $this->discount = $item['discount'];
            $this->price = $item['item_no_price']? $item['item_no_price']:$item['price'];
            $this->qty = $item['qty'];
        }        

        //Estado del formulario:
        $this->form_on = true;
    }

    /**
     * 6. Reset formulario.
     */
    public function resetForm(){
        $this->action = 'save';
        $this->field_readonly = false;
        $this->reset(['concept', 'block', 'discount', 'price', 'qty', 'observations']);
    }

    /**
     * 7. Ordenar item hacia arriba.
     */
    public function itemUp($item){
        if($item){
            $new_order = $item['budget_order'];

            //Orden del elemento anterior:
            $prev = Item::select('id', 'budget_order')
            ->where('budget_order_id', $item['budget_order_id'])
            ->where('budget_form_block_id', $item['budget_form_block_id'])
            ->where('budget_order', '<', $item['budget_order'])
            ->orderBy('budget_order', 'DESC')
            ->first();

            if($prev){            
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
     * 7.1. Ordenar itema hacia abajo.
     */
    public function itemDown($item){
        if($item){
            $new_order = $item['budget_order'];

            //Orden del elemento siguiente:
            $next = Item::select('id', 'budget_order')
            ->where('budget_order_id', $item['budget_order_id'])
            ->where('budget_form_block_id', $item['budget_form_block_id'])
            ->where('budget_order', '>', $item['budget_order'])
            ->orderBy('budget_order', 'ASC')
            ->first();

            //Orden del elemento seleccionado:
            if($next){
                Item::where('id', $item['id'])
                ->update(['budget_order' => $next->budget_order]);

                $next->budget_order = $new_order;
                $next->save();

                $this->render();
            }
        }    
    }
}

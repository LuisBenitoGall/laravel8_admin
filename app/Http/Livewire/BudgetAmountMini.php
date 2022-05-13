<?php

namespace App\Http\Livewire;

use Livewire\Component;

class BudgetAmountMini extends Component{
    /**
     * 1. Render componente.
     * 2. Actualizando importe.
     */
    
    public $budget_amount, $currency;

    protected $listeners = ['updateAmountMini'];

    public function mount($budget_amount, $currency){
        $this->budget_amount = $budget_amount;
        $this->currency = $currency;
    }
    
    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.budget-amount-mini');
    }

    /**
     * 2. Actualizando importe.
     */
    public function updateAmountMini($amount_updated){
        $this->budget_amount = $amount_updated;
    }
}

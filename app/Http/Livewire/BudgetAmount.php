<?php

namespace App\Http\Livewire;

use Livewire\Component;

class BudgetAmount extends Component{
    /**
     * 1. Render componente.
     * 2. Actualizando importe.
     */
    
    public $bag_amount, $budget_amount, $budget_amount_without_discount, $currency, $environment;

    protected $listeners = ['updateAmount'];
    
    public function mount($budget_amount, $currency, $budget_amount_without_discount, $environment, $bag_amount = false){
        $this->bag_amount = $bag_amount;
        $this->budget_amount = $budget_amount;
        $this->budget_amount_without_discount = $budget_amount_without_discount;
        $this->currency = $currency;
        $this->environment = $environment;
    }
    
    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.budget-amount');
    }

    /**
     * 2. Actualizando importe.
     */
    public function updateAmount($amount_updated, $budget_amount_without_discount = false){
        $this->budget_amount = $amount_updated;
        $this->budget_amount_without_discount = $budget_amount_without_discount;
    }
}

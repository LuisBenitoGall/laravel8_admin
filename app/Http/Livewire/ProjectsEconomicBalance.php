<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Helpers\Helpers;

//Models:
use App\Models\Expense;
use App\Models\Order;
use App\Models\Project;

class ProjectsEconomicBalance extends Component{
    /**
     * 1. Render componente.
     */

    public $expenses;
    public $list = [];
    public $orders_amount;
    public $project;
    public $purchases_amount;

    public function mount($project){
        $this->project = $project;

        //Ventas:
        $orders = Order::select('orders.id')
        ->join('project_orders', 'orders.id', '=', 'project_orders.order_id')
        ->where('project_orders.project_id', $project->id)
        ->get();

        foreach($orders as $order){
            $amount = $order->getAmount();
            $this->orders_amount = $this->orders_amount + $amount['total_amount'];   
        }

        //Compras:
        $purchases = Order::select('orders.id')
        ->join('project_purchases', 'orders.id', '=', 'project_purchases.order_id')
        ->where('project_purchases.project_id', $project->id)
        ->get();

        foreach($purchases as $purchase){
            $amount = $purchase->getAmount();
            $this->purchases_amount = $this->purchases_amount + $amount['total_amount'];   
        }

        //Gastos:
        $this->expenses = Expense::projectExpenses($project->id);

        //Pasamos array list con los valores:
        $this->list[] = $this->orders_amount;
        $this->list[] = $this->purchases_amount;
        $this->list[] = $this->expenses;
    }

    public function render(){
        return view('livewire.projects-economic-balance');
    }
}

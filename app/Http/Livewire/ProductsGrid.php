<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Product;

class ProductsGrid extends Component{
    /**
     * 1. Render componente.
     * 2. Cargar nuevos registros.
     */
    
    public $totalRecords;
    public $loadRecords = 12;
    public $company;
    public $currency;
    public $order_ = false;
    public $customer;
    public $sale;

    public function mount($currency, $order_, $company, $customer, $sale = false){
        $this->company = $company? $company->id:session('currentCompany');
        $this->sale = $sale;
        $data = Product::companyProducts($this->company, $this->company, $this->sale, false);
        $this->totalRecords = $data->count();

        $this->currency = $currency['symbol'];
        $this->order_ = $order_;
        $this->order = $customer? $customer:session('currentCompany');
    }
     
    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.products-grid')->with('products', Product::companyProducts($this->company, $this->customer, $this->sale, false, $this->loadRecords));
    }

    /**
     * 2. Cargar nuevos registros.
     */
    public function loadMore(){
        $this->loadRecords += 12;
    }
}

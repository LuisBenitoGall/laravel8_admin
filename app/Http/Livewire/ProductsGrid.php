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

    public function mount($currency, $order_, $company){
        $this->company = $company? $company->id:session('currentCompany');

        $data = Product::companyProducts($this->company, $this->company, false, false);
        $this->totalRecords = $data->count();
        $this->currency = $currency['symbol'];
        $this->order_ = $order_;
    }
     
    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.products-grid')->with('products', Product::companyProducts($this->company, false, false, $this->loadRecords));
    }

    /**
     * 2. Cargar nuevos registros.
     */
    public function loadMore(){
        $this->loadRecords += 12;
    }
}

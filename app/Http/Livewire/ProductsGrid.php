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
    public $currency;
    public $order_ = false;

    public function mount($currency, $order_){
        $data = Product::companyProducts(session('currentCompany'), false, false);
        $this->totalRecords = $data->count();
        $this->currency = $currency['symbol'];
        $this->order_ = $order_;
    }
     
    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.products-grid')->with('products', Product::companyProducts(session('currentCompany'), false, false, $this->loadRecords));
    }

    /**
     * 2. Cargar nuevos registros.
     */
    public function loadMore(){
        $this->loadRecords += 12;
    }
}

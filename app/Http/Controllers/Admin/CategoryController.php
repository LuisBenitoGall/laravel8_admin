<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Category;

class CategoryController extends Controller{
    /**
     * 1. Listado de categorías por módulo.
     * 1.1. Listado de categorías por módulo para DataTable.
     */
    
    /**
     * 1. Listado de categorías por módulo.
     */
    public function index(){
        $previous_url = url()->previous();

        dd($previous_url);

        $header = [
            'option' => '', 
            'suboption' => trans('textos.categorias'),
            'module' => '',
            'slug' => 'categories'
        ];

        return view('admin.categories.index')->with(compact('header'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\ProjectGroup;
use App\Models\ProjectGroupItems;

class ProjectGroupItemsController extends Controller{
    /**
     * 
     */
    
    private $option = 'proyectos';
    private $module = 'projects';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:projects');
    }
}

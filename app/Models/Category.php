<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model{
    /**
     * 1. Obtener nombre de la categoría madre.
     * 2. Categorías principales por módulo y empresa. Array.
     * 3. Categoría padre de subcategoría.
     * 4. Subcategorías de categoría.
     * 5. Proyectos por categoría.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'categories'; 

    protected $dates = ['deleted_at'];

    /**
     * 1. Obtener nombre de la categoría madre.
     */
    public function getCategoryName($subcategory){
        $category = Category::select('name')->where('id', $subcategory)->first();
        return $category->name;
    }

    /**
     * 2. Categorías principales por módulo y empresa. Array.
     */
    public static function getMainCategories($module_id, $company_id){
        $data = Category::where('company_id', $company_id)
        ->where('module_id', $module_id)
        ->where('subcategory', '0')
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        return $data;
    }

    /**
     * 2.1. Todas las categorías por módulo y empresa.
     */
    public function getModuleCategories($module_id, $company_id){
        $data = Category::where('company_id', $company_id)
        ->where('module_id', $module_id)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        return $data;    
    }

    /**
     * 2.1. Todas las categorías por módulo y empresa. STATIC
     */
    public static function getModuleCategoriesStatic($module_id, $company_id){
        $data = Category::where('company_id', $company_id)
        ->where('module_id', $module_id)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        return $data;    
    }

    /**
     * 3. Categoría padre de subcategoría.
     */
    public function sub_category(){
        return $this->belongsTo(self::class, 'subcategory', 'id');
    }

    /**
     * 4. Subcategorías de categoría.
     */
    public function parent_category(){
        return $this->hasMany(self::class, 'id', 'subcategory');
    }

    /**
     * 5. Proyectos por categoría.
     */
    public function projects(){
        return $this->belongsToMany(Project::class, 'project_categories');
    }
}

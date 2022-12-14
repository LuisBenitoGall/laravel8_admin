<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingConcept extends Model{
    /**
     * 1. Obtener nombre del concepto padre.
     */
     
    use HasFactory;
    use SoftDeletes;

    protected $table = 'accounting_concepts';

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'slug', 'description', 'rate', 'duration', 'status'];    

    /**
     * 1. Obtener nombre del concepto padre.
     */
    public function getConceptName($subconcept){
        $concept = AccountingConcept::select('name')->where('id', $subconcept)->first();
        return $concept->name;
    }
}

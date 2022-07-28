<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model{
    /**
     * 1. Total gastos por proyecto.
     */

    use HasFactory;

    protected $table = 'expenses';

    /**
     * 1. Total gastos por proyecto.
     */
    public static function projectExpenses($project_id){
        $data = Expense::where('project_id', $project_id)
        ->sum('amount_with_iva');

        return $data;
    }
}

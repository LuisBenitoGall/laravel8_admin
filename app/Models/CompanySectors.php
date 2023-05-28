<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySectors extends Model{
    use HasFactory;

    protected $table = 'company_sectors';

    protected $fillable = ['company_id', 'category_id'];
}

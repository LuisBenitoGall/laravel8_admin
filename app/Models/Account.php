<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model{
    use HasFactory;
    use SoftDeletes;

	protected $table = 'accounts';

    protected $dates = ['deleted_at'];

    protected $fillable = ['company_id', 'name', 'slug', 'description', 'main', 'status']; 	

}

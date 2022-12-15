<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPurchases extends Model{
    use HasFactory;

    protected $table = 'project_purchases';

    protected $fillable = ['project_id', 'order_id'];
}

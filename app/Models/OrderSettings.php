<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSettings extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'order_settings';

    protected $dates = ['deleted_at'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_template_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

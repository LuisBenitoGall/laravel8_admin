<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    protected $table = 'provinces';

    protected $fillable = ['name', 'slug', 'country_id', 'status'];

    public function towns()
    {
        return $this->hasMany(Town::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}

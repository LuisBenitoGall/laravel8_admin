<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    protected $table = 'countries';

    protected $fillable = ['name', 'slug', 'code', 'alfa2', 'alfa3', 'flag', 'status'];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }
}

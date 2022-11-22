<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevicePush extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['user_id', 'token','onesignal'];

    protected $searchableFields = ['*'];

    protected $table = 'device_pushes';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

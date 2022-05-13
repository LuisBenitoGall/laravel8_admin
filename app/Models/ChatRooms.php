<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRooms extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'chat_rooms';

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'description', 'status'];
}

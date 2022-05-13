<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model{
    /**
     * 1. Marcar notificación como leída.
     */

    protected $table = 'notifications';    

    /**
     * 1. Marcar notificación como leída.
     */
    static function markAsRead($notification){
        $notification->read_at = Carbon::now();
        $notification->save();
    }
}

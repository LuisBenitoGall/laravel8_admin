<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserImage extends Model{
    use HasFactory;
    protected $table = 'user_images';

    /**
	 * Relación con user:
	 */
	public function user(){
    	return $this->belongsTo(User::class);
    }	
}

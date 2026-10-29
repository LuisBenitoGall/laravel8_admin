<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = ['company_id', 'related_company_id', 'user_id', 'position', 'is_primary'];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function relatedCompany(){
        return $this->belongsTo(Company::class, 'related_company_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function docs(){
        return $this->hasMany(ContactDoc::class);
    }
}

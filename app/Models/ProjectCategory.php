<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model{
    use HasFactory;

    protected $table = 'project_categories';

    protected $fillable = ['project_id', 'category_id'];

    /**
     * 1. Relación con projects.
     */
    public function project(){
        return $this->belongsTo(Project::class);
    }     
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectGroup extends Model{
    /**
     * 1. Proyectos del grupo.
     */

    use HasFactory;
    use SoftDeletes; //Implementamos borrado lógico

    protected $dates = ['deleted_at']; 
    
    protected $table = 'project_groups';

    /**
     * 1. Proyectos del grupo.
     */
    public function projects(){
        return ProjectGroupItems::select('projects.id', 'projects.project_num', 'projects.name', 'projects.project_old_num')
        ->join('projects', 'project_group_items.project_id', '=', 'projects.id')
        ->where('project_group_items.group_id', $this->id)
        ->orderBy('projects.name')
        ->get();
    }
}

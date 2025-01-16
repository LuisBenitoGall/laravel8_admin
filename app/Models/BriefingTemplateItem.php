<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BriefingTemplateItem extends Model{
    /**
     * 1. Autoreferencia.
     */

    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'briefing_template_items';

    /**
     * 1. Autoreferencia.
     */
    public static function getRecid($recid) {
        return self::where('recid', $recid)->first();
    }
}

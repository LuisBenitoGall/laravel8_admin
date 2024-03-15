<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Amortization
 *
 * @property int $id
 * @property int|null $company_id
 * @property string|null $code
 * @property string|null $description
 * @property string|null $permission
 * @property string|null $date
 * @property float $amount
 * @property int|null $amortization_account
 * @property int|null $amortization_dota
 * @property int|null $year
 * @property int|null $month
 * @property float $amortization_pendent
 * @property string|null $create_date
 * @property string|null $modify_date
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string|null $status
 * @property string|null $deletion_date
 * @property string|null $amopro
 * @property string|null $amofac
 * @property float $amopor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization query()
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereAmofac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereAmopor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereAmopro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereAmortizationAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereAmortizationDota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereAmortizationPendent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereCreateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereDeletionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereModifyDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Amortization whereYear($value)
 * @mixin \Eloquent
 */
class Amortization extends Model{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }   

    public function lines(){
        return $this->hasMany(AmortizationLine::class);
    }

    //Suma de importe de líneas:
    public function linesAmount(){
        return AmortizationLine::where('amortization_id', $this->id)->sum('amount');    
    }

    //Actualizando importe pendiente de líneas:
    public function updatePendentAmount(){
        $total = $this->linesAmount();
        $this->amortization_pendent = $this->amortization_pendent - $total;
        $this->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\AmortizationLine
 *
 * @property int $id
 * @property int|null $amortization_id
 * @property float $amount
 * @property float $percent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines query()
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines whereAmortizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AmortizationLines withoutTrashed()
 * @mixin \Eloquent
 */
class AmortizationLine extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'amortization_lines';

}

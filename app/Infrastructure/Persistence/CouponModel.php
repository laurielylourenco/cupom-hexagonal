<?php

namespace App\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

/**
 * INFRAESTRUTURA (detalhe do adaptador de saída).
 *
 * Model Eloquent usado apenas pelo DatabaseCouponRepository. O domínio
 * não conhece esta classe.
 */
class CouponModel extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'percentage',
        'expires_at',
        'usage_limit',
        'times_used',
    ];

    protected $casts = [
        'percentage' => 'float',
        'expires_at' => 'immutable_datetime',
        'usage_limit' => 'integer',
        'times_used' => 'integer',
    ];
}

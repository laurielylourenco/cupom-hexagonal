<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Coupon;
use App\Domain\CouponRepositoryInterface;
use DateTimeImmutable;

/**
 * ADAPTADOR DE SAÍDA (driven adapter).
 *
 * Implementa a porta CouponRepositoryInterface usando Eloquent/banco.
 * Converte entre registro (CouponModel) e entidade de domínio (Coupon).
 */
class DatabaseCouponRepository implements CouponRepositoryInterface
{
    public function findByCode(string $code): ?Coupon
    {
        $model = CouponModel::where('code', $code)->first();

        if ($model === null) {
            return null;
        }

        return new Coupon(
            $model->code,
            (float) $model->percentage,
            DateTimeImmutable::createFromInterface($model->expires_at),
            $model->usage_limit,
            (int) $model->times_used,
        );
    }

    public function save(Coupon $coupon): void
    {
        CouponModel::updateOrCreate(
            ['code' => $coupon->getCode()],
            [
                'percentage' => $coupon->getPercentage(),
                'expires_at' => $coupon->getExpiresAt(),
                'usage_limit' => $coupon->getUsageLimit(),
                'times_used' => $coupon->getTimesUsed(),
            ],
        );
    }
}

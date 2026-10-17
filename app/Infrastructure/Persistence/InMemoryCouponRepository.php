<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Coupon;
use App\Domain\CouponRepositoryInterface;

/**
 * ADAPTADOR DE SAÍDA (driven adapter) para testes.
 *
 * Guarda os cupons em um array, sem banco, permitindo testar os use cases
 * sem Laravel.
 */
class InMemoryCouponRepository implements CouponRepositoryInterface
{
    /** @var array<string, Coupon> */
    private array $coupons = [];

    public function findByCode(string $code): ?Coupon
    {
        return $this->coupons[$code] ?? null;
    }

    public function save(Coupon $coupon): void
    {
        $this->coupons[$coupon->getCode()] = $coupon;
    }
}

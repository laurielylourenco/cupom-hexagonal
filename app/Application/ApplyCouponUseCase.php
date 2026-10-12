<?php

namespace App\Application;

use App\Domain\CouponNotFoundException;
use App\Domain\CouponRepositoryInterface;
use DateTimeImmutable;

class ApplyCouponUseCase
{
    public function __construct(private CouponRepositoryInterface $repository) {}

    public function execute(string $code, float $amount, DateTimeImmutable $now): float
    {
        $coupon = $this->repository->findByCode($code);

        if ($coupon === null) {
            throw CouponNotFoundException::forCode($code);
        }

        $discount = $coupon->calculateDiscount($amount, $now);

        $coupon->registerUsage($now);

        $this->repository->save($coupon);

        return $discount;
    }
}

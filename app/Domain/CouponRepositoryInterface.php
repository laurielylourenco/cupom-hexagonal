<?php

namespace App\Domain;

interface CouponRepositoryInterface
{
    public function findByCode(string $code): Coupon|null;
    public function save(Coupon $coupon): void;

}
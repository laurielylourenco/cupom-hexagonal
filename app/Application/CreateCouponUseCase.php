<?php

namespace App\Application;


use App\Domain\Coupon;
use App\Domain\CouponAlreadyExistsException;
use App\Domain\CouponRepositoryInterface;
use DateTimeImmutable;

class CreateCouponUseCase
{
    public function __construct(private CouponRepositoryInterface $repository) {}

    public function execute(string $code, float $percentage, DateTimeImmutable $expiresAt, ?int $usageLimit = null): Coupon
    {
        if ($this->repository->findByCode($code) !== null) {
            throw CouponAlreadyExistsException::forCode($code);
        }

        $coupon = new Coupon($code, $percentage, $expiresAt, $usageLimit);
        $this->repository->save($coupon);
        return $coupon;
    }
}
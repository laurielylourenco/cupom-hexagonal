<?php

namespace Tests\Unit\Application;

use App\Application\ApplyCouponUseCase;
use App\Domain\Coupon;
use App\Domain\CouponNotFoundException;
use App\Domain\CouponNotUsableException;
use App\Infrastructure\Persistence\InMemoryCouponRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Testa o use case com InMemoryCouponRepository — sem banco e sem Laravel.
 */
class ApplyCouponUseCaseTest extends TestCase
{
    private DateTimeImmutable $now;

    private DateTimeImmutable $future;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = new DateTimeImmutable('2026-06-01 12:00:00');
        $this->future = new DateTimeImmutable('2026-12-31 23:59:59');
    }

    public function test_applies_coupon_and_registers_usage(): void
    {
        $repository = new InMemoryCouponRepository;
        $repository->save(new Coupon('PROMO10', 10.0, $this->future, usageLimit: 5));
        $useCase = new ApplyCouponUseCase($repository);

        $discount = $useCase->execute('PROMO10', 200.0, $this->now);

        $this->assertSame(20.0, $discount);
        $this->assertSame(1, $repository->findByCode('PROMO10')->getTimesUsed());
    }

    public function test_missing_coupon_throws_not_found(): void
    {
        $useCase = new ApplyCouponUseCase(new InMemoryCouponRepository);

        $this->expectException(CouponNotFoundException::class);

        $useCase->execute('MISSING', 100.0, $this->now);
    }

    public function test_expired_coupon_throws_not_usable(): void
    {
        $repository = new InMemoryCouponRepository;
        $repository->save(new Coupon('OLD', 10.0, new DateTimeImmutable('2025-01-01')));
        $useCase = new ApplyCouponUseCase($repository);

        $this->expectException(CouponNotUsableException::class);

        $useCase->execute('OLD', 100.0, $this->now);
    }

    public function test_exhausted_usage_limit_throws_not_usable(): void
    {
        $repository = new InMemoryCouponRepository;
        $repository->save(new Coupon('SPENT', 10.0, $this->future, usageLimit: 1, timesUsed: 1));
        $useCase = new ApplyCouponUseCase($repository);

        $this->expectException(CouponNotUsableException::class);

        $useCase->execute('SPENT', 100.0, $this->now);
    }
}

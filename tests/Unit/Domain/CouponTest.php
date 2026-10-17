<?php

namespace Tests\Unit\Domain;

use App\Domain\Coupon;
use App\Domain\CouponNotUsableException;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Testes unitários do domínio — PHP puro, sem Laravel.
 *
 * Extende PHPUnit\Framework\TestCase (não Tests\TestCase) para não
 * subir o framework: o Domain não depende de Illuminate.
 */
class CouponTest extends TestCase
{
    private DateTimeImmutable $now;

    private DateTimeImmutable $future;

    private DateTimeImmutable $past;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = new DateTimeImmutable('2026-06-01 12:00:00');
        $this->future = new DateTimeImmutable('2026-12-31 23:59:59');
        $this->past = new DateTimeImmutable('2025-01-01 00:00:00');
    }

    public function test_percentage_outside_range_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Coupon('BAD', 150, $this->future);
    }

    public function test_is_expired_when_now_is_after_expires_at(): void
    {
        $coupon = new Coupon('OLD', 10, $this->past);

        $this->assertTrue($coupon->isExpired($this->now));
        $this->assertFalse($coupon->canBeUsed($this->now));
    }

    public function test_is_not_expired_before_expires_at(): void
    {
        $coupon = new Coupon('OK', 10, $this->future);

        $this->assertFalse($coupon->isExpired($this->now));
        $this->assertTrue($coupon->canBeUsed($this->now));
    }

    public function test_has_reached_usage_limit(): void
    {
        $coupon = new Coupon('LIMIT', 10, $this->future, usageLimit: 2, timesUsed: 2);

        $this->assertTrue($coupon->hasReachedUsageLimit());
        $this->assertFalse($coupon->canBeUsed($this->now));
    }

    public function test_null_usage_limit_never_reaches_limit(): void
    {
        $coupon = new Coupon('UNLIMITED', 10, $this->future, usageLimit: null, timesUsed: 999);

        $this->assertFalse($coupon->hasReachedUsageLimit());
        $this->assertTrue($coupon->canBeUsed($this->now));
    }

    public function test_calculate_discount_returns_percentage_of_amount(): void
    {
        $coupon = new Coupon('PROMO10', 10, $this->future);

        $this->assertSame(20.0, $coupon->calculateDiscount(200.0, $this->now));
    }

    public function test_calculate_discount_on_expired_coupon_throws(): void
    {
        $coupon = new Coupon('EXPIRED', 10, $this->past);

        $this->expectException(CouponNotUsableException::class);

        $coupon->calculateDiscount(100.0, $this->now);
    }

    public function test_calculate_discount_when_usage_limit_reached_throws(): void
    {
        $coupon = new Coupon('SPENT', 10, $this->future, usageLimit: 1, timesUsed: 1);

        $this->expectException(CouponNotUsableException::class);

        $coupon->calculateDiscount(100.0, $this->now);
    }

    public function test_register_usage_increments_times_used(): void
    {
        $coupon = new Coupon('USE', 10, $this->future, usageLimit: 5);

        $coupon->registerUsage($this->now);

        $this->assertSame(1, $coupon->getTimesUsed());
    }

    public function test_register_usage_on_expired_coupon_throws(): void
    {
        $coupon = new Coupon('EXPIRED', 10, $this->past);

        $this->expectException(CouponNotUsableException::class);

        $coupon->registerUsage($this->now);
    }
}

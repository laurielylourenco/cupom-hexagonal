<?php

namespace Tests\Unit\Application;

use App\Application\CreateCouponUseCase;
use App\Domain\Coupon;
use App\Domain\CouponAlreadyExistsException;
use App\Infrastructure\Persistence\InMemoryCouponRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Testa o use case com o adaptador InMemory — sem banco e sem Laravel.
 */
class CreateCouponUseCaseTest extends TestCase
{
    public function test_creates_and_persists_coupon(): void
    {
        $repository = new InMemoryCouponRepository;
        $useCase = new CreateCouponUseCase($repository);
        $expiresAt = new DateTimeImmutable('2026-12-31');

        $coupon = $useCase->execute('PROMO10', 10.0, $expiresAt, 100);

        $this->assertSame('PROMO10', $coupon->getCode());
        $this->assertSame(10.0, $coupon->getPercentage());
        $this->assertSame(100, $coupon->getUsageLimit());
        $this->assertNotNull($repository->findByCode('PROMO10'));
    }

    public function test_duplicate_code_throws_already_exists(): void
    {
        $repository = new InMemoryCouponRepository;
        $repository->save(new Coupon('DUP', 5.0, new DateTimeImmutable('2026-12-31')));
        $useCase = new CreateCouponUseCase($repository);

        $this->expectException(CouponAlreadyExistsException::class);

        $useCase->execute('DUP', 10.0, new DateTimeImmutable('2026-12-31'));
    }
}

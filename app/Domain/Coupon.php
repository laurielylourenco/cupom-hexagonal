<?php

namespace App\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

class Coupon
{
    public function __construct(
        private string $code,
        private float $percentage,
        private DateTimeImmutable $expiresAt,
        private ?int $usageLimit = null,
        private int $timesUsed = 0,
    ) {
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Percentage must be between 0 and 100.');
        }
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isExpired(DateTimeImmutable $now): bool
    {
        return $now > $this->expiresAt;
    }

    public function hasReachedUsageLimit(): bool
    {
        return $this->usageLimit !== null && $this->timesUsed >= $this->usageLimit;
    }

    public function canBeUsed(DateTimeImmutable $now): bool
    {
        return !$this->isExpired($now) && !$this->hasReachedUsageLimit();
    }

    /**
     * Retorna o valor do desconto (não o total final).
     */
    public function calculateDiscount(float $amount, DateTimeImmutable $now): float
    {
        $this->assertCanBeUsed($now);

        return $amount * $this->percentage / 100;
    }

    public function registerUsage(DateTimeImmutable $now): void
    {
        $this->assertCanBeUsed($now);

        $this->timesUsed++;
    }

    private function assertCanBeUsed(DateTimeImmutable $now): void
    {
        if (!$this->canBeUsed($now)) {
            throw CouponNotUsableException::forCode($this->code);
        }
    }
}

<?php

namespace App\Domain;

use DomainException;

class CouponNotFoundException extends DomainException
{
    public static function forCode(string $code): self
    {
        return new self("Coupon {$code} not found.");
    }
}

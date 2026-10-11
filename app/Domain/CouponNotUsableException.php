<?php

namespace App\Domain;

use DomainException;

class CouponNotUsableException extends DomainException
{
    public static function forCode(string $code): self
    {
        return new self("Coupon {$code} is expired or has reached its usage limit.");
    }
}

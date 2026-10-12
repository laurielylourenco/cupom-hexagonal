<?php

namespace App\Domain;

use DomainException;

class CouponAlreadyExistsException extends DomainException
{
    public static function forCode(string $code): self
    {
        return new self("Coupon {$code} already exists.");
    }
}

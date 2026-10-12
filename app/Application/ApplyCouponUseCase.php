<?php

namespace App\Application;

use App\Domain\CouponNotFoundException;
use App\Domain\CouponRepositoryInterface;
use DateTimeImmutable;

/**
 * APPLICATION + PORTA DE ENTRADA (driving port).
 *
 * O adaptador de entrada (CouponController) chama este use case para entrar
 * no núcleo. Ele só orquestra o fluxo: as decisões (expirado, esgotado, quanto
 * de desconto) ficam no Coupon e a persistência é feita pela porta
 * CouponRepositoryInterface.
 */
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

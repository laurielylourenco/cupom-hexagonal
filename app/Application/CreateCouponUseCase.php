<?php

namespace App\Application;


use App\Domain\Coupon;
use App\Domain\CouponAlreadyExistsException;
use App\Domain\CouponRepositoryInterface;
use DateTimeImmutable;

/**
 * APPLICATION + PORTA DE ENTRADA (driving port).
 *
 * O adaptador de entrada (CouponController) chama este use case para entrar
 * no núcleo. Ele só orquestra o fluxo: as regras de negócio ficam no Coupon e
 * a persistência é feita pela porta CouponRepositoryInterface.
 */
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
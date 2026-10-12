<?php

namespace App\Domain;
// porta de saída para o repositório de cupons
/**
 * PORTA DE SAÍDA (driven port).
 *
 * Contrato que o núcleo define para dizer do que precisa para persistir
 * cupons, sem saber como isso é feito. Quem implementa é um adaptador de
 * saída (ex.: DatabaseCouponRepository, InMemoryCouponRepository), que fica
 * na Infrastructure.
 */
interface CouponRepositoryInterface
{
    public function findByCode(string $code): Coupon|null;
    public function save(Coupon $coupon): void;

}
<?php

namespace App\Infrastructure\Http\Responses;

use App\Domain\Coupon;
use Illuminate\Http\JsonResponse;

/**
 * ADAPTADOR DE ENTRADA — formato da resposta HTTP.
 *
 * O domínio devolve entidades/valores; esta classe decide como isso aparece
 * no JSON da API. Assim o Coupon não precisa saber de HTTP, e o controller
 * não espalha arrays de resposta em vários lugares.
 */
class CouponResponse
{
    /**
     * Resposta após criar um cupom (201 Created).
     */
    public static function created(Coupon $coupon): JsonResponse
    {
        return response()->json(self::toArray($coupon), 201);
    }

    /**
     * Resposta após aplicar um cupom: só o valor do desconto calculado.
     */
    public static function applied(float $discount): JsonResponse
    {
        return response()->json([
            'discount' => $discount,
        ]);
    }

    /**
     * Converte a entidade de domínio no shape público da API.
     *
     * @return array<string, mixed>
     */
    public static function toArray(Coupon $coupon): array
    {
        return [
            'code' => $coupon->getCode(),
            'percentage' => $coupon->getPercentage(),
            // ISO 8601 — fácil de ler em qualquer cliente HTTP
            'expires_at' => $coupon->getExpiresAt()->format(DATE_ATOM),
            'usage_limit' => $coupon->getUsageLimit(),
            'times_used' => $coupon->getTimesUsed(),
        ];
    }
}

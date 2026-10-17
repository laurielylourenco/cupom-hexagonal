<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\ApplyCouponUseCase;
use App\Application\CreateCouponUseCase;
use App\Infrastructure\Http\Requests\ApplyCouponRequest;
use App\Infrastructure\Http\Requests\CreateCouponRequest;
use App\Infrastructure\Http\Responses\CouponResponse;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * ADAPTADOR DE ENTRADA (driving adapter) — HTTP.
 *
 * Papel na arquitetura hexagonal:
 * 1. Recebe o request HTTP
 * 2. Extrai/converte dados (já validados pelo Form Request)
 * 3. Chama a porta de entrada (use case) — o núcleo da aplicação
 * 4. Traduz o retorno do domínio para JSON via CouponResponse
 *
 * Não contém regra de negócio: não calcula desconto, não decide se o cupom
 * é válido, etc. Isso fica no Domain / Application.
 *
 * Não estende App\Http\Controllers\Controller: nessa arquitetura o adaptador
 * HTTP vive em Infrastructure; a pasta app/Http do skeleton não é necessária.
 */
class CouponController
{
    /**
     * POST /api/coupons
     *
     * Cria um cupom novo. O Form Request já garantiu o formato dos campos;
     * aqui só montamos os argumentos tipados e delegamos ao use case.
     */
    #[OA\Post(
        path: '/api/coupons',
        operationId: 'createCoupon',
        summary: 'Cria um cupom',
        tags: ['Cupons'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCoupon'),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Cupom criado',
                content: new OA\JsonContent(ref: '#/components/schemas/Coupon'),
            ),
            new OA\Response(
                response: 409,
                description: 'Já existe um cupom com esse código',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorMessage'),
            ),
            new OA\Response(
                response: 422,
                description: 'Dados inválidos',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
    public function store(
        CreateCouponRequest $request,
        CreateCouponUseCase $createCoupon,
    ): JsonResponse {
        $coupon = $createCoupon->execute(
            code: $request->string('code')->toString(),
            percentage: (float) $request->input('percentage'),
            expiresAt: new DateTimeImmutable($request->string('expires_at')->toString()),
            usageLimit: $request->filled('usage_limit')
                ? $request->integer('usage_limit')
                : null,
        );

        return CouponResponse::created($coupon);
    }

    /**
     * POST /api/coupons/{code}/apply
     *
     * Aplica o cupom sobre um valor (amount). O {code} vem da URL;
     * o amount vem do body (já validado).
     *
     * Usamos "agora" (DateTimeImmutable) para o domínio decidir expiração.
     */
    #[OA\Post(
        path: '/api/coupons/{code}/apply',
        operationId: 'applyCoupon',
        summary: 'Aplica um cupom sobre um valor',
        tags: ['Cupons'],
        parameters: [
            new OA\PathParameter(
                name: 'code',
                description: 'Código do cupom',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'PROMO10'),
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ApplyCoupon'),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Desconto calculado',
                content: new OA\JsonContent(ref: '#/components/schemas/AppliedCoupon'),
            ),
            new OA\Response(
                response: 404,
                description: 'Cupom não encontrado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorMessage'),
            ),
            new OA\Response(
                response: 422,
                description: 'Cupom expirado, esgotado ou dados inválidos',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorMessage'),
            ),
        ],
    )]
    public function apply(
        string $code,
        ApplyCouponRequest $request,
        ApplyCouponUseCase $applyCoupon,
    ): JsonResponse {
        $discount = $applyCoupon->execute(
            code: $code,
            amount: (float) $request->input('amount'),
            now: new DateTimeImmutable,
        );

        return CouponResponse::applied($discount);
    }
}

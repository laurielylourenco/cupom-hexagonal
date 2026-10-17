<?php

namespace App\Infrastructure\Http;

use OpenApi\Attributes as OA;

/**
 * Contrato OpenAPI do adaptador HTTP.
 *
 * Fica na infraestrutura de entrada: descreve rotas, payloads e respostas.
 * O domínio e os casos de uso não participam desta documentação.
 */
#[OA\Info(
    version: '1.0.0',
    title: 'Cupom Hexagonal',
    description: 'API para criar cupons de desconto e aplicá-los sobre um valor de compra.',
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Aplicação local',
)]
#[OA\Tag(
    name: 'Cupons',
    description: 'Criar e aplicar cupons de desconto',
)]
#[OA\Schema(
    schema: 'CreateCoupon',
    required: ['code', 'percentage', 'expires_at'],
    properties: [
        new OA\Property(property: 'code', type: 'string', maxLength: 50, example: 'PROMO10'),
        new OA\Property(property: 'percentage', type: 'number', format: 'float', maximum: 100, minimum: 0, example: 10),
        new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', example: '2026-12-31T23:59:59+00:00'),
        new OA\Property(property: 'usage_limit', type: 'integer', minimum: 1, nullable: true, example: 100),
    ],
)]
#[OA\Schema(
    schema: 'Coupon',
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'PROMO10'),
        new OA\Property(property: 'percentage', type: 'number', format: 'float', example: 10),
        new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', example: '2026-12-31T23:59:59+00:00'),
        new OA\Property(property: 'usage_limit', type: 'integer', nullable: true, example: 100),
        new OA\Property(property: 'times_used', type: 'integer', example: 0),
    ],
)]
#[OA\Schema(
    schema: 'ApplyCoupon',
    required: ['amount'],
    properties: [
        new OA\Property(property: 'amount', description: 'Valor da compra sobre o qual o desconto é calculado', type: 'number', format: 'float', minimum: 0, example: 200),
    ],
)]
#[OA\Schema(
    schema: 'AppliedCoupon',
    properties: [
        new OA\Property(property: 'discount', type: 'number', format: 'float', example: 20),
    ],
)]
#[OA\Schema(
    schema: 'ErrorMessage',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Coupon PROMO10 already exists.'),
    ],
)]
#[OA\Schema(
    schema: 'ValidationError',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The code field is required.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            example: ['code' => ['The code field is required.']],
        ),
    ],
)]
class OpenApi {}

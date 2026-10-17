<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de feature dos endpoints HTTP (adaptador de entrada).
 *
 * Sobe o Laravel, usa SQLite em memória (phpunit.xml) e RefreshDatabase
 * para migrar a tabela coupons a cada teste.
 */
class CouponApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_coupon_returns_201(): void
    {
        $response = $this->postJson('/api/coupons', [
            'code' => 'PROMO10',
            'percentage' => 10,
            'expires_at' => '2026-12-31T23:59:59+00:00',
            'usage_limit' => 100,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('code', 'PROMO10')
            ->assertJsonPath('percentage', 10)
            ->assertJsonPath('usage_limit', 100)
            ->assertJsonPath('times_used', 0);

        $this->assertDatabaseHas('coupons', [
            'code' => 'PROMO10',
            'percentage' => 10,
        ]);
    }

    public function test_create_duplicate_coupon_returns_409(): void
    {
        $payload = [
            'code' => 'DUP',
            'percentage' => 5,
            'expires_at' => '2026-12-31T23:59:59+00:00',
        ];

        $this->postJson('/api/coupons', $payload)->assertCreated();

        $this->postJson('/api/coupons', $payload)
            ->assertStatus(409)
            ->assertJsonPath('message', 'Coupon DUP already exists.');
    }

    public function test_apply_coupon_returns_discount(): void
    {
        $this->postJson('/api/coupons', [
            'code' => 'PROMO10',
            'percentage' => 10,
            'expires_at' => '2026-12-31T23:59:59+00:00',
            'usage_limit' => 5,
        ])->assertCreated();

        $response = $this->postJson('/api/coupons/PROMO10/apply', [
            'amount' => 200,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('discount', 20);

        $this->assertDatabaseHas('coupons', [
            'code' => 'PROMO10',
            'times_used' => 1,
        ]);
    }

    public function test_apply_missing_coupon_returns_404(): void
    {
        $this->postJson('/api/coupons/MISSING/apply', ['amount' => 100])
            ->assertNotFound()
            ->assertJsonPath('message', 'Coupon MISSING not found.');
    }

    public function test_apply_exhausted_coupon_returns_422(): void
    {
        $this->postJson('/api/coupons', [
            'code' => 'ONCE',
            'percentage' => 10,
            'expires_at' => '2026-12-31T23:59:59+00:00',
            'usage_limit' => 1,
        ])->assertCreated();

        $this->postJson('/api/coupons/ONCE/apply', ['amount' => 100])->assertOk();

        $this->postJson('/api/coupons/ONCE/apply', ['amount' => 100])
            ->assertStatus(422)
            ->assertJsonPath(
                'message',
                'Coupon ONCE is expired or has reached its usage limit.',
            );
    }

    public function test_openapi_document_lists_coupon_endpoints(): void
    {
        config(['l5-swagger.defaults.generate_always' => true]);

        $response = $this->get('/docs');

        $response->assertOk();
        $response->assertJsonPath('info.title', 'Cupom Hexagonal');
        $response->assertJsonPath('paths./api/coupons.post.operationId', 'createCoupon');
        $response->assertJsonPath('paths./api/coupons/{code}/apply.post.operationId', 'applyCoupon');
    }

    public function test_create_coupon_validation_errors_return_422(): void
    {
        $this->postJson('/api/coupons', [
            'code' => '',
            'percentage' => 150,
        ])->assertStatus(422);
    }
}

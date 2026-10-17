<?php

namespace Tests\Feature;

use Tests\TestCase;

class CouponPageTest extends TestCase
{
    public function test_home_mounts_the_coupon_counter(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('coupons.index');
        $response->assertSee('<div id="app"', false);
        $response->assertSee('lang="pt-BR"', false);

        $html = $response->getContent();

        $this->assertIsString($html);
        $this->assertTrue(
            str_contains($html, 'resources/js/app.js') || str_contains($html, '/build/assets/'),
            'A página deve carregar o entry do Vite.',
        );
    }
}

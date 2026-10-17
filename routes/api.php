<?php

use App\Infrastructure\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API (adaptador de entrada HTTP)
|--------------------------------------------------------------------------
|
| O prefixo /api é adicionado automaticamente pelo bootstrap/app.php
| quando registramos o arquivo com withRouting(api: ...).
|
| Fluxo: Request → Form Request (validação) → Controller → Use Case → Domain
|
*/

// Cria um cupom novo
Route::post('/coupons', [CouponController::class, 'store']);

// Aplica um cupom existente sobre um valor de compra
Route::post('/coupons/{code}/apply', [CouponController::class, 'apply']);

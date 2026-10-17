<?php

use App\Domain\CouponAlreadyExistsException;
use App\Domain\CouponNotFoundException;
use App\Domain\CouponNotUsableException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // Registra routes/api.php com prefixo /api automaticamente
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Em rotas /api/*, sempre responder erro em JSON (mesmo sem Accept: application/json)
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /*
         * Tradução domínio → HTTP
         *
         * As exceções abaixo nascem no núcleo (Domain), sem código de status HTTP.
         * O adaptador de entrada (esta camada) decide o status adequado para a API.
         * Assim o domínio continua puro e a API fica consistente.
         */

        // Cupom não encontrado no repositório
        $exceptions->render(function (CouponNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 404);
            }
        });

        // Tentativa de criar cupom com código já existente (conflito)
        $exceptions->render(function (CouponAlreadyExistsException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 409);
            }
        });

        // Cupom expirado ou com limite de uso esgotado (dado válido, regra de negócio falhou)
        $exceptions->render(function (CouponNotUsableException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
        });
    })->create();

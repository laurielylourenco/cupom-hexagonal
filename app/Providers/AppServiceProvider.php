<?php

namespace App\Providers;

use App\Domain\CouponRepositoryInterface;
use App\Infrastructure\Persistence\DatabaseCouponRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Aqui o container do Laravel liga a PORTA (interface do domínio) ao
     * ADAPTADOR DE SAÍDA concreto (repositório com Eloquent/banco).
     *
     * Assim CreateCouponUseCase / ApplyCouponUseCase pedem
     * CouponRepositoryInterface e recebem DatabaseCouponRepository
     * sem conhecerem o banco.
     */
    public function register(): void
    {
        $this->app->bind(
            CouponRepositoryInterface::class,
            DatabaseCouponRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

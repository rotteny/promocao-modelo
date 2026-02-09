<?php

namespace App\Providers;

use App\Contracts\InvoiceValidatorInterface;
use App\Services\MockInvoiceValidator;
use App\Services\PromocaoService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binding da interface de validação de cupom fiscal.
        // Para usar a API real da Sefaz, substituir MockInvoiceValidator
        // pela implementação concreta.
        $this->app->bind(InvoiceValidatorInterface::class, MockInvoiceValidator::class);

        // PromocaoService como singleton para evitar múltiplas consultas ao DB
        $this->app->singleton(PromocaoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paginação com Bootstrap 5
        Paginator::useBootstrapFive();

        // Compartilha o status da promoção com todas as views (para o layout)
        View::composer('layouts.app', function ($view) {
            $view->with('promocaoAtiva', app(PromocaoService::class)->isAtiva());
        });
    }
}

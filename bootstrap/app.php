<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redireciona guests para a tela de login correta
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.login');
            }
            return route('login');
        });

        // Redireciona usuários já logados para o dashboard correto
        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.dashboard');
            }
            return route('dashboard');
        });

        // Registra alias do middleware
        $middleware->alias([
            'promocao.ativa' => \App\Http\Middleware\VerificarPromocaoAtiva::class,
            'admin.permission' => \App\Http\Middleware\CheckAdminPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

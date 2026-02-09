<?php

use App\Http\Controllers\Admin\AdminCampanhaController;
use App\Http\Controllers\Admin\AdminCupomController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminFilaController;
use App\Http\Controllers\Admin\AdminNotificacaoController;
use App\Http\Controllers\Admin\AdminNumeroDaSorteController;
use App\Http\Controllers\Admin\AdminParticipanteController;
use App\Http\Controllers\Admin\AdminProdutoController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CupomFiscalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/regulamento', [PageController::class, 'regulamento'])->name('regulamento');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// API pública de status da promoção (consumida via JavaScript)
Route::get('/api/promocao/status', [PageController::class, 'statusPromocao'])->name('api.promocao.status');

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação - Participante (guard: web)
|--------------------------------------------------------------------------
| Cadastro exige promoção ativa. Login permite acesso mesmo após encerramento
| (para o participante consultar seus números).
*/

Route::middleware('guest')->group(function () {
    Route::get('/cadastro', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/cadastro', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rotas do Participante (Autenticado - guard: web)
|--------------------------------------------------------------------------
| Dashboard sempre acessível. Cadastro de cupom exige promoção ativa.
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Cadastro de cupom exige promoção ativa
    Route::middleware('promocao.ativa')->group(function () {
        Route::get('/cupom/cadastrar', [CupomFiscalController::class, 'create'])->name('cupom.create');
        Route::post('/cupom/cadastrar', [CupomFiscalController::class, 'store'])->name('cupom.store');
        Route::post('/cupom/consultar-qrcode', [CupomFiscalController::class, 'consultarQrCode'])->name('cupom.consultar-qrcode');
    });
});

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação - Admin (guard: admin)
|--------------------------------------------------------------------------
*/

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login']);
});

Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout')->middleware('auth:admin');

/*
|--------------------------------------------------------------------------
| Rotas do Admin (Autenticado - guard: admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard e Gráficos (acesso livre a todos os admins)
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/chart/cadastros-diarios', [AdminDashboardController::class, 'chartCadastrosDiarios'])->name('chart.cadastros');
    Route::get('/chart/cupons-semanais', [AdminDashboardController::class, 'chartCuponsSemanais'])->name('chart.cupons');
    Route::get('/chart/numeros-distribuidos', [AdminDashboardController::class, 'chartNumerosDistribuidos'])->name('chart.numeros');

    // Fila de Processamento (acesso livre a todos os admins)
    Route::post('/fila/desbloquear', [AdminFilaController::class, 'desbloquear'])->name('fila.desbloquear');
    Route::post('/fila/reprocessar/{cupom}', [AdminFilaController::class, 'reprocessar'])->name('fila.reprocessar');
    Route::get('/cupons-erro', [AdminFilaController::class, 'cuponsComErro'])->name('cupons.erro');

    // Notificações (acesso livre a todos os admins)
    Route::post('/notificacoes/marcar-lidas', [AdminNotificacaoController::class, 'marcarLidas'])->name('notificacoes.lidas');

    // Listagens e Relatórios (acesso livre a todos os admins)
    Route::get('/participantes', [AdminParticipanteController::class, 'index'])->name('participantes.index');
    Route::get('/participantes/exportar', [AdminParticipanteController::class, 'exportar'])->name('participantes.exportar');
    Route::get('/participantes/{participante}', [AdminParticipanteController::class, 'show'])->name('participantes.show');

    Route::get('/cupons', [AdminCupomController::class, 'index'])->name('cupons.index');
    Route::get('/cupons/exportar', [AdminCupomController::class, 'exportar'])->name('cupons.exportar');
    Route::get('/cupons/{cupom}', [AdminCupomController::class, 'show'])->name('cupons.show');

    Route::get('/numeros-da-sorte', [AdminNumeroDaSorteController::class, 'index'])->name('numeros.index');
    Route::get('/numeros-da-sorte/exportar', [AdminNumeroDaSorteController::class, 'exportar'])->name('numeros.exportar');

    // === Rotas protegidas por permissão ===

    // Controle da Campanha (requer perm_encerrar_campanha)
    Route::middleware('admin.permission:perm_encerrar_campanha')->group(function () {
        Route::post('/campanha/encerrar', [AdminCampanhaController::class, 'encerrar'])->name('campanha.encerrar');
        Route::post('/campanha/reabrir', [AdminCampanhaController::class, 'reabrir'])->name('campanha.reabrir');
    });

    // Configurações (requer perm_configuracoes)
    Route::middleware('admin.permission:perm_configuracoes')->group(function () {
        Route::get('/configuracoes', [AdminSettingController::class, 'index'])->name('settings');
        Route::post('/configuracoes', [AdminSettingController::class, 'update'])->name('settings.update');
    });

    // Produtos (requer perm_produtos)
    Route::middleware('admin.permission:perm_produtos')->group(function () {
        Route::get('/produtos', [AdminProdutoController::class, 'index'])->name('produtos');
        Route::post('/produtos', [AdminProdutoController::class, 'store'])->name('produtos.store');
        Route::delete('/produtos/{produto}', [AdminProdutoController::class, 'destroy'])->name('produtos.destroy');
    });

    // FAQ (requer perm_faq)
    Route::middleware('admin.permission:perm_faq')->group(function () {
        Route::get('/faqs', [AdminFaqController::class, 'index'])->name('faqs.index');
        Route::get('/faqs/criar', [AdminFaqController::class, 'create'])->name('faqs.create');
        Route::post('/faqs', [AdminFaqController::class, 'store'])->name('faqs.store');
        Route::get('/faqs/{faq}/editar', [AdminFaqController::class, 'edit'])->name('faqs.edit');
        Route::put('/faqs/{faq}', [AdminFaqController::class, 'update'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [AdminFaqController::class, 'destroy'])->name('faqs.destroy');
    });

    // Gerenciamento de Administradores (somente super_admin)
    Route::middleware('admin.permission:is_super_admin')->group(function () {
        Route::get('/usuarios', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/usuarios/criar', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/usuarios', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/usuarios/{user}/editar', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/usuarios/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/usuarios/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});

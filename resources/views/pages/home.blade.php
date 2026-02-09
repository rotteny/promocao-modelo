@extends('layouts.app')

@section('title', 'Promoção Modelo - Concorra a Prêmios Incríveis!')

@section('content')
<!-- Banner de Status da Promoção -->
@if($promocao->isAguardando())
    <div class="bg-info text-dark py-3">
        <div class="container text-center">
            <h5 class="mb-1"><i class="bi bi-calendar-event me-2"></i>A promoção ainda não começou!</h5>
            <p class="mb-0">
                Início previsto: <strong>{{ $promocao->getDataInicio()->format('d/m/Y \à\s H:i') }}</strong>
                <span id="countdownInicio" class="ms-2 badge bg-dark"></span>
            </p>
        </div>
    </div>
@elseif($promocao->isEncerrada())
    <div class="bg-danger text-white py-3">
        <div class="container text-center">
            <h5 class="mb-1"><i class="bi bi-trophy-fill me-2"></i>Promoção Encerrada!</h5>
            <p class="mb-0">{{ $promocao->getMensagemStatus() }}</p>
        </div>
    </div>
@elseif($promocao->isAtiva())
    <div class="bg-success text-white py-2">
        <div class="container text-center">
            <small>
                <i class="bi bi-broadcast me-1"></i><strong>Promoção ativa!</strong> Cadastre seus cupons fiscais e concorra a prêmios.
            </small>
        </div>
    </div>
@endif

<!-- Hero Section -->
<section class="bg-pm-gradient text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="bi bi-trophy-fill me-2"></i>Promoção Modelo
                </h1>
                <p class="lead mb-4 opacity-90">
                    @if($promocao->isEncerrada())
                        A promoção foi encerrada. Aguarde a data do sorteio para conferir os resultados!
                        Se já está cadastrado, faça login para consultar seus números da sorte.
                    @elseif($promocao->isAguardando())
                        Em breve você poderá cadastrar seus cupons fiscais e concorrer a prêmios incríveis!
                        Acompanhe nossas redes sociais para mais informações.
                    @else
                        Cadastre seus cupons fiscais e concorra a prêmios incríveis!
                        A cada <strong>R$ 20,00</strong> em compras de produtos participantes você ganha <strong>1 número da sorte</strong>.
                        Compre <strong>produtos bônus</strong> e ganhe o <strong>dobro de números</strong>!
                    @endif
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    @if($promocao->isAtiva())
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-bold px-4">
                                <i class="bi bi-person-plus me-2"></i>Cadastre-se Grátis
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Já tenho conta
                            </a>
                        @else
                            <a href="{{ route('cupom.create') }}" class="btn btn-light btn-lg fw-bold px-4">
                                <i class="bi bi-receipt me-2"></i>Cadastrar Cupom
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-grid-1x2 me-2"></i>Meus Números
                            </a>
                        @endguest
                    @else
                        @auth('web')
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg fw-bold px-4">
                                <i class="bi bi-grid-1x2 me-2"></i>Consultar Meus Números
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Já tenho conta
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <div class="p-4">
                    @if($promocao->isEncerrada())
                        <i class="bi bi-hourglass-bottom" style="font-size: 10rem; opacity: 0.3;"></i>
                    @else
                        <i class="bi bi-gift-fill" style="font-size: 10rem; opacity: 0.3;"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if(!$promocao->isEncerrada())
<!-- Como Funciona -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">
            <i class="bi bi-question-circle me-2 text-primary"></i>Como Funciona?
        </h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-pm-gradient d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-person-plus-fill text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="fw-bold">1. Cadastre-se</h5>
                        <p class="text-muted">
                            Crie sua conta gratuitamente com seus dados pessoais para participar da promoção.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-pm-gradient d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-receipt text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="fw-bold">2. Cadastre seus Cupons</h5>
                        <p class="text-muted">
                            Insira os dados do cupom fiscal manualmente ou escaneie o QR Code da nota.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-pm-gradient d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-trophy-fill text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="fw-bold">3. Concorra!</h5>
                        <p class="text-muted">
                            Ganhe números da sorte automaticamente e concorra a prêmios incríveis nos sorteios.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Regras Resumidas -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">
                    <i class="bi bi-stars me-2" style="color: var(--pm-secondary);"></i>Ganhe mais números!
                </h2>
                <div class="d-flex align-items-start mb-3">
                    <div class="bg-pm-gradient rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 40px; height: 40px;">
                        <span class="text-white fw-bold">1</span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">R$ 20,00 = 1 Número da Sorte</h6>
                        <p class="text-muted mb-0">A cada R$ 20,00 em compras de produtos participantes, você recebe automaticamente 1 número da sorte.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <div class="bg-pm-gradient rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 40px; height: 40px;">
                        <span class="text-white fw-bold">2</span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Produtos Bônus = Números em Dobro!</h6>
                        <p class="text-muted mb-0">Produtos bônus contam duas vezes: além dos números base, você ganha +1 número extra a cada R$ 20,00 em produtos bônus.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="bg-pm-gradient rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 40px; height: 40px;">
                        <span class="text-white fw-bold">3</span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">10 Séries de Sorteio</h6>
                        <p class="text-muted mb-0">Os números são distribuídos em 10 séries (0 a 9), cada uma com 10.000 números.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="card bg-dark text-white p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-calculator me-2"></i>Exemplo</h5>
                    <table class="table table-dark table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td>Produtos normais:</td>
                                <td class="fw-bold text-end">R$ 25,00</td>
                            </tr>
                            <tr>
                                <td>Produtos bônus:</td>
                                <td class="fw-bold text-end text-success">R$ 40,00</td>
                            </tr>
                            <tr class="border-top border-secondary">
                                <td>Total do cupom:</td>
                                <td class="fw-bold text-end">R$ 65,00</td>
                            </tr>
                            <tr>
                                <td>Números base (65 &divide; 20):</td>
                                <td class="fw-bold text-end">3 números</td>
                            </tr>
                            <tr>
                                <td>Bônus extra (40 &divide; 20):</td>
                                <td class="fw-bold text-end text-success">+2 números</td>
                            </tr>
                            <tr class="border-top border-secondary">
                                <td class="fw-bold">Total:</td>
                                <td class="fw-bold text-end text-warning fs-5">5 números</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5 text-center">
    <div class="container">
        @if($promocao->isAtiva())
            <h2 class="fw-bold mb-3">Não perca tempo!</h2>
            <p class="lead text-muted mb-4">Cadastre-se agora e comece a acumular seus números da sorte.</p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-pm btn-lg">
                    <i class="bi bi-rocket-takeoff me-2"></i>Participar Agora
                </a>
            @else
                <a href="{{ route('cupom.create') }}" class="btn btn-pm btn-lg">
                    <i class="bi bi-receipt me-2"></i>Cadastrar Cupom Fiscal
                </a>
            @endguest
        @else
            <h2 class="fw-bold mb-3">Fique de olho!</h2>
            <p class="lead text-muted mb-4">A promoção começa em {{ $promocao->getDataInicio()->format('d/m/Y \à\s H:i') }}.</p>
        @endif
    </div>
</section>
@else
<!-- Promoção Encerrada - Aguardar Sorteio -->
<section class="py-5 text-center">
    <div class="container">
        <div class="py-5">
            <i class="bi bi-hourglass-split text-warning" style="font-size: 5rem;"></i>
            <h2 class="fw-bold mt-4 mb-3">Aguarde o Sorteio!</h2>
            <p class="lead text-muted mb-4">
                A fase de cadastros foi encerrada. Agora é só aguardar a data do sorteio
                para saber se você foi um dos premiados!
            </p>
            @auth('web')
                <a href="{{ route('dashboard') }}" class="btn btn-pm btn-lg">
                    <i class="bi bi-grid-1x2 me-2"></i>Consultar Meus Números
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-pm-outline btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login para consultar números
                </a>
            @endauth
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dataInicio = new Date('{{ $promocao->getDataInicio()->toIso8601String() }}');
    const statusAtual = '{{ $promocao->getStatus() }}';

    function atualizarCountdown(elementId, dataAlvo, label) {
        const el = document.getElementById(elementId);
        if (!el) return;

        function tick() {
            const agora = new Date();
            const diff = dataAlvo - agora;

            if (diff <= 0) {
                el.textContent = label;
                // Recarrega a página quando o countdown terminar
                setTimeout(() => window.location.reload(), 1500);
                return;
            }

            const dias = Math.floor(diff / (1000 * 60 * 60 * 24));
            const horas = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((diff % (1000 * 60)) / 1000);

            let texto = '';
            if (dias > 0) texto += dias + 'd ';
            if (horas > 0 || dias > 0) texto += horas + 'h ';
            texto += minutos + 'm ' + segundos + 's';

            el.textContent = texto;
            setTimeout(tick, 1000);
        }

        tick();
    }

    if (statusAtual === 'aguardando') {
        atualizarCountdown('countdownInicio', dataInicio, 'Começou!');
    }
});
</script>
@endpush

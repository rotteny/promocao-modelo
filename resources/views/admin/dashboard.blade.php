@extends('layouts.admin')

@section('title', 'Painel Admin - Promoção Modelo')
@section('page-title', 'Dashboard')

@section('content')

    <!-- Status da Campanha -->
    <div class="card mb-4 {{ $promocao->isAtiva() ? 'border-success' : ($promocao->isEncerrada() ? 'border-danger' : 'border-info') }}">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @if($promocao->isAtiva())
                        <span class="badge bg-success fs-6 me-2"><i class="bi bi-broadcast me-1"></i>CAMPANHA ATIVA</span>
                        <span class="text-muted">Até {{ $promocao->getDataFim()->format('d/m/Y H:i') }}</span>
                        <span class="ms-3 text-muted">|</span>
                        <span class="ms-3"><i class="bi bi-stars me-1"></i>{{ number_format($promocao->getNumerosDisponiveis()) }} números disponíveis</span>
                    @elseif($promocao->isAguardando())
                        <span class="badge bg-info fs-6 me-2"><i class="bi bi-calendar-event me-1"></i>AGUARDANDO INÍCIO</span>
                        <span class="text-muted">Início: {{ $promocao->getDataInicio()->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="badge bg-danger fs-6 me-2"><i class="bi bi-lock-fill me-1"></i>CAMPANHA ENCERRADA</span>
                        <span class="text-muted">{{ $promocao->getMensagemStatus() }}</span>
                    @endif
                </div>
                <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-gear me-1"></i>Configurações
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta de Fila Bloqueada -->
    @if($filaBloqueada)
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <i class="bi bi-exclamation-octagon-fill fs-1 me-3"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">
                        <i class="bi bi-lock-fill me-1"></i>Fila de Processamento Bloqueada
                    </h5>
                    <p class="mb-2">
                        O processamento de números da sorte está <strong>parado</strong>.
                        @if($cupomComErro)
                            Cupom <strong>#{{ $cupomComErro->numero }}</strong>
                            (Participante: {{ $cupomComErro->participante?->name ?? 'N/A' }}) falhou com o erro:
                        @endif
                    </p>
                    @if($cupomComErro && $cupomComErro->erro_processamento)
                        <div class="bg-dark text-light p-2 rounded mb-3 small font-monospace">
                            {{ $cupomComErro->erro_processamento }}
                        </div>
                    @endif
                    <div class="d-flex gap-2">
                        @if($cupomComErro)
                            <form action="{{ route('admin.fila.reprocessar', $cupomComErro) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Reprocessar Cupom
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.fila.desbloquear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-unlock-fill me-1"></i>Desbloquear Fila
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Notificações não lidas -->
    @if($notificacoes->isNotEmpty())
        <div class="card border-warning mb-4">
            <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-bell-fill me-2 text-warning"></i>Notificações
                    <span class="badge bg-warning text-dark">{{ $notificacoes->count() }}</span>
                </h6>
                <form action="{{ route('admin.notificacoes.lidas') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-check2-all me-1"></i>Marcar todas como lidas
                    </button>
                </form>
            </div>
            <div class="list-group list-group-flush">
                @foreach($notificacoes as $notificacao)
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1">
                                @if($notificacao->data['tipo'] === 'erro_processamento_cupom')
                                    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                                    {{ $notificacao->data['mensagem'] }}
                                @else
                                    {{ $notificacao->data['mensagem'] ?? 'Notificação' }}
                                @endif
                            </p>
                            <small class="text-muted">{{ $notificacao->created_at->diffForHumans() }}</small>
                        </div>
                        @if(isset($notificacao->data['erro']))
                            <small class="text-muted font-monospace">{{ Str::limit($notificacao->data['erro'], 100) }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Cards Estatísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('admin.participantes.index') }}" class="card text-center p-3 text-decoration-none">
                <div class="text-primary mb-1"><i class="bi bi-people-fill fs-3"></i></div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_participantes']) }}</div>
                <div class="text-muted small">Participantes</div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('admin.cupons.index') }}" class="card text-center p-3 text-decoration-none">
                <div class="text-success mb-1"><i class="bi bi-receipt fs-3"></i></div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_cupons']) }}</div>
                <div class="text-muted small">Total Cupons</div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('admin.cupons.index', ['status' => 'concluido']) }}" class="card text-center p-3 text-decoration-none">
                <div class="text-info mb-1"><i class="bi bi-check-circle fs-3"></i></div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($stats['cupons_concluidos']) }}</div>
                <div class="text-muted small">Concluídos</div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('admin.cupons.index', ['status' => 'validado']) }}" class="card text-center p-3 text-decoration-none">
                <div class="text-warning mb-1"><i class="bi bi-hourglass-split fs-3"></i></div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($stats['cupons_na_fila']) }}</div>
                <div class="text-muted small">Na Fila</div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('admin.cupons.index', ['status' => 'erro']) }}" class="card text-center p-3 text-decoration-none">
                <div class="{{ $stats['cupons_erro'] > 0 ? 'text-danger' : 'text-secondary' }} mb-1">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <div class="fs-4 fw-bold {{ $stats['cupons_erro'] > 0 ? 'text-danger' : 'text-dark' }}">
                    {{ number_format($stats['cupons_erro']) }}
                </div>
                <div class="text-muted small">Com Erro</div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('admin.numeros.index') }}" class="card text-center p-3 text-decoration-none">
                <div class="text-danger mb-1"><i class="bi bi-stars fs-3"></i></div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_numeros']) }}</div>
                <div class="text-muted small">Nº da Sorte</div>
            </a>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-4">
        <!-- Participantes Cadastrados -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-graph-up me-2 text-primary"></i>Participantes Cadastrados (Últimos 20 dias)
                    </h6>
                </div>
                <div class="card-body py-2">
                    <canvas id="chartCadastros" height="160"></canvas>
                </div>
            </div>
        </div>

        <!-- Cupons Semanais -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-bar-chart me-2 text-success"></i>Cupons Cadastrados (Semanal)
                    </h6>
                </div>
                <div class="card-body py-2">
                    <canvas id="chartCupons" height="160"></canvas>
                </div>
            </div>
        </div>

        <!-- Números da Sorte - Progresso -->
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="display-5 fw-bold" style="color: var(--pm-primary);">{{ number_format($stats['total_numeros']) }}</div>
                            <div class="text-muted small">Números Distribuídos</div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small"><i class="bi bi-stars me-1"></i>Capacidade de Números da Sorte</span>
                                <span class="fw-bold small" style="color: var(--pm-primary);">
                                    @php $pctNumeros = $stats['total_numeros'] / max(100000, 1) * 100; @endphp
                                    {{ number_format($pctNumeros, 1) }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-pm-gradient" role="progressbar"
                                     style="width: {{ $pctNumeros }}%;" aria-valuenow="{{ $pctNumeros }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mt-3 mt-md-0">
                            <div class="display-5 fw-bold text-muted">{{ number_format(100000 - $stats['total_numeros']) }}</div>
                            <div class="text-muted small">Disponíveis</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Cadastros Diários
        fetch('{{ route("admin.chart.cadastros") }}')
            .then(r => {
                if (!r.ok) { console.error('Chart cadastros HTTP error:', r.status, r.statusText); return r.text().then(t => { console.error('Response body:', t); return null; }); }
                return r.json();
            })
            .then(data => {
                if (!data) return;
                console.log('Chart cadastros data:', data);
                new Chart(document.getElementById('chartCadastros'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Novos Cadastros',
                            data: data.data,
                            borderColor: '#6f42c1',
                            backgroundColor: 'rgba(111, 66, 193, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            })
            .catch(err => console.error('Chart cadastros fetch error:', err));

        // Gráfico de Cupons Semanais
        fetch('{{ route("admin.chart.cupons") }}')
            .then(r => {
                if (!r.ok) { console.error('Chart cupons HTTP error:', r.status, r.statusText); return r.text().then(t => { console.error('Response body:', t); return null; }); }
                return r.json();
            })
            .then(data => {
                if (!data) return;
                console.log('Chart cupons data:', data);
                new Chart(document.getElementById('chartCupons'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Cupons Cadastrados',
                            data: data.data,
                            backgroundColor: 'rgba(25, 135, 84, 0.7)',
                            borderColor: '#198754',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            })
            .catch(err => console.error('Chart cupons fetch error:', err));

    });
</script>
@endpush

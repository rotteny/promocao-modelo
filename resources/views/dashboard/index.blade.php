@extends('layouts.app')

@section('title', 'Meus Números - Promoção Modelo')

@section('content')
<div class="container">
    @if($promocao->isEncerrada())
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-trophy-fill fs-3 me-3 text-warning"></i>
                <div>
                    <h6 class="alert-heading mb-1">Promoção Encerrada</h6>
                    <p class="mb-0">{{ $promocao->getMensagemStatus() }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-grid-1x2 me-2"></i>Meus Números da Sorte</h2>
            <p class="text-muted mb-0">Olá, <strong>{{ Auth::guard('web')->user()->name }}</strong>! Confira seus números abaixo.</p>
        </div>
        @if($promocao->isAtiva())
            <a href="{{ route('cupom.create') }}" class="btn btn-pm">
                <i class="bi bi-plus-circle me-1"></i>Novo Cupom
            </a>
        @endif
    </div>

    <!-- Resumo -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-pm-gradient rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-stars text-white fs-4"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $numerosDaSorte->count() }}</div>
                        <div class="text-muted small">Números da Sorte</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-pm-gradient rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-receipt text-white fs-4"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $cuponsFiscais->count() }}</div>
                        <div class="text-muted small">Cupons Cadastrados</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-pm-gradient rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-cash-stack text-white fs-4"></i>
                    </div>
                    <div>
                        <div class="stat-number">R$ {{ number_format($cuponsFiscais->sum('valor_total'), 2, ',', '.') }}</div>
                        <div class="text-muted small">Total em Compras</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Números da Sorte -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold"><i class="bi bi-stars me-2 text-warning"></i>Meus Números</h5>
        </div>
        <div class="card-body">
            @if($numerosDaSorte->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Você ainda não possui números da sorte.</p>
                    @if($cuponsFiscais->where('status', 'validado')->count() > 0 || $cuponsFiscais->where('status', 'processando')->count() > 0)
                        <div class="alert alert-info d-inline-block">
                            <i class="bi bi-hourglass-split me-1"></i>
                            Seus cupons estão na fila de processamento. Os números serão gerados em breve!
                        </div>
                    @else
                        <a href="{{ route('cupom.create') }}" class="btn btn-pm">
                            <i class="bi bi-receipt me-1"></i>Cadastrar meu primeiro cupom
                        </a>
                    @endif
                </div>
            @else
                <div class="row g-2">
                    @foreach($numerosDaSorte as $ns)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="lucky-number-badge w-100 justify-content-center">
                                {{ $ns->numero_formatado }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Histórico de Cupons -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Histórico de Cupons</h5>
        </div>
        <div class="card-body p-0">
            @if($cuponsFiscais->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">Nenhum cupom cadastrado ainda.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nº Cupom</th>
                                <th>CNPJ Loja</th>
                                <th>Data da Compra</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                                <th>Números Ganhos</th>
                                <th>Cadastro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuponsFiscais as $cupom)
                                <tr>
                                    <td class="fw-semibold">{{ $cupom->numero }}</td>
                                    <td class="small font-monospace">
                                        @if($cupom->cnpj_loja)
                                            {{ substr($cupom->cnpj_loja, 0, 2) }}.{{ substr($cupom->cnpj_loja, 2, 3) }}.{{ substr($cupom->cnpj_loja, 5, 3) }}/{{ substr($cupom->cnpj_loja, 8, 4) }}-{{ substr($cupom->cnpj_loja, 12, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $cupom->data_compra?->format('d/m/Y') ?? '-' }}</td>
                                    <td>R$ {{ number_format($cupom->valor_total, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $cupom->status_badge }}">
                                            <i class="{{ $cupom->status_icon }} me-1"></i>{{ $cupom->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(in_array($cupom->status, ['validado', 'processando']))
                                            <span class="badge bg-info text-dark rounded-pill">
                                                <i class="bi bi-hourglass-split me-1"></i>Processando...
                                            </span>
                                        @else
                                            <span class="badge bg-primary rounded-pill">{{ $cupom->numeros_da_sorte_count }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $cupom->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

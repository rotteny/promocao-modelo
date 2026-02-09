@extends('layouts.admin')

@section('title', 'Cupons Fiscais - Admin')
@section('page-title', 'Cupons Fiscais')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-receipt me-2"></i>Cupons Fiscais</h2>
            <p class="text-muted mb-0">{{ number_format($totalCupons) }} cupons cadastrados</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cupons.exportar', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Resumo por Status -->
    <div class="row g-2 mb-4">
        @php
            $statusConfig = [
                '' => ['label' => 'Todos', 'icon' => 'bi-list-ul', 'bg' => 'bg-secondary', 'count' => $totalCupons],
                'pendente' => ['label' => 'Pendentes', 'icon' => 'bi-clock', 'bg' => 'bg-secondary', 'count' => $statusCounts['pendente']],
                'validado' => ['label' => 'Na Fila', 'icon' => 'bi-hourglass-split', 'bg' => 'bg-info', 'count' => $statusCounts['validado']],
                'processando' => ['label' => 'Processando', 'icon' => 'bi-arrow-repeat', 'bg' => 'bg-warning', 'count' => $statusCounts['processando']],
                'concluido' => ['label' => 'Concluídos', 'icon' => 'bi-check-circle', 'bg' => 'bg-success', 'count' => $statusCounts['concluido']],
                'erro' => ['label' => 'Com Erro', 'icon' => 'bi-exclamation-triangle', 'bg' => 'bg-danger', 'count' => $statusCounts['erro']],
                'rejeitado' => ['label' => 'Rejeitados', 'icon' => 'bi-x-circle', 'bg' => 'bg-dark', 'count' => $statusCounts['rejeitado']],
            ];
        @endphp
        @foreach($statusConfig as $key => $cfg)
            <div class="col">
                <a href="{{ route('admin.cupons.index', array_merge(request()->only('busca'), $key ? ['status' => $key] : [])) }}"
                   class="card text-center p-2 text-decoration-none {{ $status === $key || ($status === null && $key === '') ? 'border-primary border-2' : '' }}">
                    <div class="small">
                        <span class="badge {{ $cfg['bg'] }} rounded-pill">{{ $cfg['count'] }}</span>
                    </div>
                    <div class="small fw-semibold mt-1 text-dark">
                        <i class="bi {{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Busca -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.cupons.index') }}" method="GET" class="row g-2 align-items-end">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="col-md-9">
                    <label for="busca" class="form-label small fw-semibold">Buscar cupom</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="busca" id="busca" class="form-control"
                               placeholder="Número do cupom, nome ou CPF do participante..." value="{{ $busca }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-pm flex-grow-1">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                    @if($busca || $status)
                        <a href="{{ route('admin.cupons.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($busca)
        <div class="alert alert-info py-2 small">
            <i class="bi bi-info-circle me-1"></i>
            Resultados para: <strong>"{{ $busca }}"</strong>
            @if($status) | Status: <strong>{{ $statusConfig[$status]['label'] ?? $status }}</strong> @endif
            ({{ $cupons->total() }} encontrado(s))
        </div>
    @endif

    <!-- Tabela -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">ID</th>
                        <th>Número do Cupom</th>
                        <th>CNPJ Loja</th>
                        <th>Participante</th>
                        <th class="text-end">Valor (R$)</th>
                        <th>Status</th>
                        <th class="text-center" width="80">Nº Sorte</th>
                        <th width="120">Data Compra</th>
                        <th width="140">Cadastro</th>
                        <th width="70"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cupons as $cupom)
                        <tr>
                            <td class="text-muted small">{{ $cupom->id }}</td>
                            <td class="font-monospace fw-semibold">{{ $cupom->numero }}</td>
                            <td class="small font-monospace">
                                @if($cupom->cnpj_loja)
                                    {{ substr($cupom->cnpj_loja, 0, 2) }}.{{ substr($cupom->cnpj_loja, 2, 3) }}.{{ substr($cupom->cnpj_loja, 5, 3) }}/{{ substr($cupom->cnpj_loja, 8, 4) }}-{{ substr($cupom->cnpj_loja, 12, 2) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $cupom->participante?->name ?? 'N/A' }}</td>
                            <td class="text-end fw-semibold">{{ number_format((float) $cupom->valor_total, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $cupom->status_badge }}">
                                    <i class="bi {{ $cupom->status_icon }} me-1"></i>{{ $cupom->status_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-pm-gradient text-white">{{ $cupom->numeros_da_sorte_count }}</span>
                            </td>
                            <td class="small">{{ $cupom->data_compra?->format('d/m/Y') ?? '-' }}</td>
                            <td class="small text-muted">{{ $cupom->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.cupons.show', $cupom) }}" class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Nenhum cupom fiscal encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    @if($cupons->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $cupons->links() }}
        </div>
    @endif
@endsection

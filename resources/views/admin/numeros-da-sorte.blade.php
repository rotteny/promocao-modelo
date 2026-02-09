@extends('layouts.admin')

@section('title', 'Números da Sorte - Admin')
@section('page-title', 'Números da Sorte')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-stars me-2"></i>Números da Sorte</h2>
            <p class="text-muted mb-0">{{ number_format($totalNumeros) }} números distribuídos de {{ number_format($capacidadeTotal) }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.numeros.exportar', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Progresso Geral -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold">Capacidade Utilizada</span>
                <span class="fw-bold" style="color: var(--pm-primary);">
                    {{ number_format($totalNumeros) }} / {{ number_format($capacidadeTotal) }}
                    ({{ number_format(($totalNumeros / max($capacidadeTotal, 1)) * 100, 2) }}%)
                </span>
            </div>
            <div class="progress" style="height: 16px;">
                <div class="progress-bar bg-pm-gradient" role="progressbar"
                     style="width: {{ ($totalNumeros / max($capacidadeTotal, 1)) * 100 }}%;">
                </div>
            </div>
        </div>
    </div>

    <!-- Séries -->
    <div class="row g-2 mb-4">
        <div class="col">
            <a href="{{ route('admin.numeros.index', request()->only('busca')) }}"
               class="card text-center p-2 text-decoration-none {{ $serie === null || $serie === '' ? 'border-primary border-2' : '' }}">
                <div class="small">
                    <span class="badge bg-secondary rounded-pill">{{ $totalNumeros }}</span>
                </div>
                <div class="small fw-semibold mt-1 text-dark">Todas</div>
            </a>
        </div>
        @for($s = 0; $s < 10; $s++)
            <div class="col">
                <a href="{{ route('admin.numeros.index', array_merge(request()->only('busca'), ['serie' => $s])) }}"
                   class="card text-center p-2 text-decoration-none {{ (string) $serie === (string) $s ? 'border-primary border-2' : '' }}">
                    <div class="small">
                        <span class="badge bg-pm-gradient text-white rounded-pill">{{ $serieCounts[$s] ?? 0 }}</span>
                    </div>
                    <div class="small fw-semibold mt-1 text-dark">Série {{ $s }}</div>
                </a>
            </div>
        @endfor
    </div>

    <!-- Busca -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.numeros.index') }}" method="GET" class="row g-2 align-items-end">
                @if($serie !== null && $serie !== '')
                    <input type="hidden" name="serie" value="{{ $serie }}">
                @endif
                <div class="col-md-9">
                    <label for="busca" class="form-label small fw-semibold">Buscar número da sorte</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="busca" id="busca" class="form-control"
                               placeholder="Nome, CPF do participante ou número..." value="{{ $busca }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-pm flex-grow-1">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                    @if($busca || ($serie !== null && $serie !== ''))
                        <a href="{{ route('admin.numeros.index') }}" class="btn btn-outline-secondary">
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
            @if($serie !== null && $serie !== '') | Série: <strong>{{ $serie }}</strong> @endif
            ({{ $numeros->total() }} encontrado(s))
        </div>
    @endif

    <!-- Tabela -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">ID</th>
                        <th class="text-center" width="80">Série</th>
                        <th class="text-center" width="100">Número</th>
                        <th width="160">Nº Formatado</th>
                        <th>Participante</th>
                        <th>CPF</th>
                        <th>Cupom Fiscal</th>
                        <th width="140">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($numeros as $ns)
                        <tr>
                            <td class="text-muted small">{{ $ns->id }}</td>
                            <td class="text-center">
                                <span class="badge bg-pm-gradient text-white">{{ $ns->serie }}</span>
                            </td>
                            <td class="text-center font-monospace fw-semibold">{{ str_pad($ns->numero, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <span class="lucky-number-badge" style="font-size: 0.85rem; padding: 0.3rem 0.6rem;">
                                    <i class="bi bi-star-fill"></i>{{ $ns->numero_formatado }}
                                </span>
                            </td>
                            <td>
                                @if($ns->participante)
                                    <a href="{{ route('admin.participantes.show', $ns->participante) }}" class="text-decoration-none fw-semibold">
                                        {{ $ns->participante->name }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="small font-monospace">{{ $ns->participante?->cpf ?? 'N/A' }}</td>
                            <td>
                                @if($ns->cupomFiscal)
                                    <a href="{{ route('admin.cupons.show', $ns->cupomFiscal) }}" class="text-decoration-none font-monospace">
                                        {{ $ns->cupomFiscal->numero }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $ns->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Nenhum número da sorte encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    @if($numeros->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $numeros->links() }}
        </div>
    @endif
@endsection

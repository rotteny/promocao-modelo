@extends('layouts.admin')

@section('title', 'Cupons com Erro - Promoção Modelo')
@section('page-title', 'Cupons com Erro')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Cupons com Erro</h2>
            <p class="text-muted mb-0">Gerencie os cupons que apresentaram erro no processamento.</p>
        </div>
        <div class="d-flex gap-2">
            @if($filaBloqueada)
                <form action="{{ route('admin.fila.desbloquear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-unlock-fill me-1"></i>Desbloquear Fila
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Voltar ao Painel
            </a>
        </div>
    </div>

    @if($filaBloqueada)
        <div class="alert alert-danger mb-4">
            <i class="bi bi-lock-fill me-2"></i>
            <strong>Fila bloqueada.</strong> O processamento de novos cupons está parado até que o problema seja resolvido.
        </div>
    @endif

    @if($cupons->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">Nenhum cupom com erro. Tudo funcionando normalmente!</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nº Cupom</th>
                                <th>Participante</th>
                                <th>Valor</th>
                                <th>Erro</th>
                                <th>Data</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cupons as $cupom)
                                <tr>
                                    <td class="text-muted">#{{ $cupom->id }}</td>
                                    <td class="fw-semibold">{{ $cupom->numero }}</td>
                                    <td>
                                        {{ $cupom->participante?->name ?? 'N/A' }}
                                        <br><small class="text-muted">{{ $cupom->participante?->email ?? '' }}</small>
                                    </td>
                                    <td>R$ {{ number_format($cupom->valor_total, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="text-danger small font-monospace" title="{{ $cupom->erro_processamento }}">
                                            {{ Str::limit($cupom->erro_processamento, 60) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $cupom->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.fila.reprocessar', $cupom) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Reprocessar">
                                                <i class="bi bi-arrow-clockwise"></i> Reprocessar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

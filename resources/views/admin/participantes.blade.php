@extends('layouts.admin')

@section('title', 'Participantes - Admin')
@section('page-title', 'Participantes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>Participantes</h2>
            <p class="text-muted mb-0">{{ number_format($totalParticipantes) }} participantes cadastrados</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.participantes.exportar', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Busca -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.participantes.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-9">
                    <label for="busca" class="form-label small fw-semibold">Buscar participante</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="busca" id="busca" class="form-control"
                               placeholder="Nome, CPF, e-mail ou cidade..." value="{{ $busca }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-pm flex-grow-1">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                    @if($busca)
                        <a href="{{ route('admin.participantes.index') }}" class="btn btn-outline-secondary">
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
            Exibindo resultados para: <strong>"{{ $busca }}"</strong>
            ({{ $participantes->total() }} encontrado(s))
        </div>
    @endif

    <!-- Tabela -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Cidade/UF</th>
                        <th class="text-center" width="80">Cupons</th>
                        <th class="text-center" width="80">Nº Sorte</th>
                        <th width="140">Cadastro</th>
                        <th width="70"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantes as $p)
                        <tr>
                            <td class="text-muted small">{{ $p->id }}</td>
                            <td class="fw-semibold">{{ $p->name }}</td>
                            <td class="small font-monospace">{{ $p->cpf }}</td>
                            <td class="small">{{ $p->email }}</td>
                            <td class="small">
                                @if($p->cidade)
                                    {{ $p->cidade }}{{ $p->estado ? '/' . $p->estado : '' }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">{{ $p->cupons_fiscais_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-pm-gradient text-white">{{ $p->numeros_da_sorte_count }}</span>
                            </td>
                            <td class="small text-muted">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.participantes.show', $p) }}" class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Nenhum participante encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    @if($participantes->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $participantes->links() }}
        </div>
    @endif
@endsection

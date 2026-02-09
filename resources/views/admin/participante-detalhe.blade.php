@extends('layouts.admin')

@section('title', $participante->name . ' - Admin')
@section('page-title', $participante->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-person-fill me-2"></i>{{ $participante->name }}</h2>
            <p class="text-muted mb-0">Detalhes do participante #{{ $participante->id }}</p>
        </div>
        <a href="{{ route('admin.participantes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar à Lista
        </a>
    </div>

    <!-- Dados Pessoais -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0"><i class="bi bi-person-vcard me-2"></i>Dados Pessoais</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Nome Completo</label>
                    <div class="fw-semibold">{{ $participante->name }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">CPF</label>
                    <div class="font-monospace">{{ $participante->cpf }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">E-mail</label>
                    <div>{{ $participante->email }}</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Telefone</label>
                    <div>{{ $participante->telefone ?? '-' }}</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Celular</label>
                    <div>{{ $participante->celular ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Endereço</label>
                    <div>
                        @if($participante->endereco)
                            {{ $participante->endereco }}{{ $participante->numero ? ', ' . $participante->numero : '' }}
                            {{ $participante->complemento ? ' - ' . $participante->complemento : '' }}<br>
                            {{ $participante->bairro ?? '' }}
                            {{ $participante->cidade ? ' - ' . $participante->cidade : '' }}{{ $participante->estado ? '/' . $participante->estado : '' }}
                            {{ $participante->cep ? ' - CEP: ' . $participante->cep : '' }}
                        @else
                            <span class="text-muted">Não informado</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Cadastrado em</label>
                    <div>{{ $participante->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card text-center p-3">
                <div class="text-primary mb-1"><i class="bi bi-receipt fs-3"></i></div>
                <div class="fs-4 fw-bold">{{ $participante->cupons_fiscais_count }}</div>
                <div class="text-muted small">Cupons Fiscais</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center p-3">
                <div class="text-success mb-1"><i class="bi bi-stars fs-3"></i></div>
                <div class="fs-4 fw-bold">{{ $participante->numeros_da_sorte_count }}</div>
                <div class="text-muted small">Números da Sorte</div>
            </div>
        </div>
    </div>

    <!-- Cupons Fiscais -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Cupons Fiscais ({{ $participante->cuponsFiscais->count() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Número</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                        <th class="text-center">Nº Sorte</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participante->cuponsFiscais as $cupom)
                        <tr>
                            <td class="font-monospace fw-semibold">{{ $cupom->numero }}</td>
                            <td>R$ {{ number_format((float) $cupom->valor_total, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $cupom->status_badge }}">
                                    <i class="bi {{ $cupom->status_icon }} me-1"></i>{{ $cupom->status_label }}
                                </span>
                            </td>
                            <td class="text-center">{{ $cupom->numeros_da_sorte_count }}</td>
                            <td class="small text-muted">{{ $cupom->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.cupons.show', $cupom) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nenhum cupom cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Números da Sorte -->
    <div class="card">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0"><i class="bi bi-stars me-2"></i>Números da Sorte ({{ $participante->numerosDaSorte->count() }})</h6>
        </div>
        <div class="card-body">
            @if($participante->numerosDaSorte->isNotEmpty())
                <div class="d-flex flex-wrap gap-2">
                    @foreach($participante->numerosDaSorte as $ns)
                        <span class="lucky-number-badge" title="Cupom: {{ $ns->cupomFiscal?->numero ?? 'N/A' }}">
                            <i class="bi bi-star-fill"></i>{{ $ns->numero_formatado }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-3 mb-0">Nenhum número da sorte gerado.</p>
            @endif
        </div>
    </div>
@endsection

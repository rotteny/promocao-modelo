@extends('layouts.admin')

@section('title', 'Cupom #' . $cupom->numero . ' - Admin')
@section('page-title', 'Cupom #' . $cupom->numero)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-receipt me-2"></i>Cupom #{{ $cupom->numero }}</h2>
            <p class="text-muted mb-0">Detalhes do cupom fiscal ID {{ $cupom->id }}</p>
        </div>
        <a href="{{ route('admin.cupons.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar à Lista
        </a>
    </div>

    <!-- Dados do Cupom -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Informações do Cupom</h6>
            <span class="badge {{ $cupom->status_badge }} fs-6">
                <i class="bi {{ $cupom->status_icon }} me-1"></i>{{ $cupom->status_label }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small text-muted">Número do Cupom</label>
                    <div class="font-monospace fw-bold fs-5">{{ $cupom->numero }}</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">CNPJ da Loja</label>
                    <div class="font-monospace fw-semibold">
                        {{ substr($cupom->cnpj_loja, 0, 2) }}.{{ substr($cupom->cnpj_loja, 2, 3) }}.{{ substr($cupom->cnpj_loja, 5, 3) }}/{{ substr($cupom->cnpj_loja, 8, 4) }}-{{ substr($cupom->cnpj_loja, 12, 2) }}
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Valor Total</label>
                    <div class="fw-bold fs-5 text-success">R$ {{ number_format((float) $cupom->valor_total, 2, ',', '.') }}</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Data da Compra</label>
                    <div class="fw-semibold">{{ $cupom->data_compra?->format('d/m/Y') ?? 'Não informada' }}</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Chave de Acesso</label>
                    <div class="small font-monospace">{{ $cupom->chave_acesso ?? 'Não informada' }}</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Data de Cadastro</label>
                    <div>{{ $cupom->created_at->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>

            @if($cupom->erro_processamento)
                <div class="alert alert-danger mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Erro no processamento:</strong> {{ $cupom->erro_processamento }}
                </div>
            @endif
        </div>
    </div>

    <!-- Participante -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0"><i class="bi bi-person me-2"></i>Participante</h6>
        </div>
        <div class="card-body">
            @if($cupom->participante)
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Nome</label>
                        <div class="fw-semibold">
                            <a href="{{ route('admin.participantes.show', $cupom->participante) }}" class="text-decoration-none">
                                {{ $cupom->participante->name }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">CPF</label>
                        <div class="font-monospace">{{ $cupom->participante->cpf }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">E-mail</label>
                        <div>{{ $cupom->participante->email }}</div>
                    </div>
                </div>
            @else
                <p class="text-muted mb-0">Participante não encontrado.</p>
            @endif
        </div>
    </div>

    <!-- Itens do Cupom -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0"><i class="bi bi-cart me-2"></i>Itens do Cupom ({{ $cupom->itens->count() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Produto</th>
                        <th class="text-center">Bônus</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-end">Valor Unit.</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cupom->itens as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->produto?->descricao ?? 'Produto removido' }}</td>
                            <td class="text-center">
                                @if($item->produto?->bonus)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Bônus</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantidade }}</td>
                            <td class="text-end">R$ {{ number_format((float) $item->valor_unitario, 2, ',', '.') }}</td>
                            <td class="text-end fw-semibold">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Total:</td>
                        <td class="text-end fw-bold text-success">R$ {{ number_format((float) $cupom->valor_total, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Números da Sorte -->
    <div class="card">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0"><i class="bi bi-stars me-2"></i>Números da Sorte ({{ $cupom->numerosDaSorte->count() }})</h6>
        </div>
        <div class="card-body">
            @if($cupom->numerosDaSorte->isNotEmpty())
                <div class="d-flex flex-wrap gap-2">
                    @foreach($cupom->numerosDaSorte as $ns)
                        <span class="lucky-number-badge">
                            <i class="bi bi-star-fill"></i>{{ $ns->numero_formatado }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-3 mb-0">
                    @if(in_array($cupom->status, ['validado', 'processando']))
                        <i class="bi bi-hourglass-split me-1"></i>Aguardando processamento...
                    @elseif($cupom->status === 'erro')
                        <i class="bi bi-exclamation-triangle me-1"></i>Nenhum número gerado devido a erro no processamento.
                    @else
                        Nenhum número da sorte gerado.
                    @endif
                </p>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Gerenciar FAQ - Admin')
@section('page-title', 'Perguntas Frequentes')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0"><i class="bi bi-question-circle me-2"></i>Perguntas Frequentes</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.faqs.create') }}" class="btn btn-pm">
                        <i class="bi bi-plus-circle me-1"></i>Nova Pergunta
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">Ordem</th>
                                    <th>Pergunta</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 130px;">Atualizado em</th>
                                    <th style="width: 140px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($faqs as $faq)
                                    <tr>
                                        <td class="text-center fw-semibold text-muted">{{ $faq->ordem }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $faq->pergunta }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 400px;">
                                                {{ Str::limit($faq->resposta, 80) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($faq->ativo)
                                                <span class="badge bg-success"><i class="bi bi-eye me-1"></i>Ativo</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="bi bi-eye-slash me-1"></i>Inativo</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $faq->updated_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja remover esta pergunta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-chat-left-text" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">Nenhuma pergunta cadastrada.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

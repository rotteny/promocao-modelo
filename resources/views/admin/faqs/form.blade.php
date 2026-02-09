@extends('layouts.admin')

@section('title', ($faq ? 'Editar' : 'Nova') . ' Pergunta - Admin')
@section('page-title', ($faq ? 'Editar' : 'Nova') . ' Pergunta')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-{{ $faq ? 'pencil' : 'plus-circle' }} me-2"></i>
                    {{ $faq ? 'Editar Pergunta' : 'Nova Pergunta' }}
                </h2>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST"
                          action="{{ $faq ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}">
                        @csrf
                        @if($faq) @method('PUT') @endif

                        <div class="mb-3">
                            <label for="pergunta" class="form-label fw-semibold">Pergunta <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('pergunta') is-invalid @enderror"
                                   id="pergunta"
                                   name="pergunta"
                                   value="{{ old('pergunta', $faq?->pergunta) }}"
                                   placeholder="Ex: Como faço para participar da promoção?"
                                   required>
                            @error('pergunta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="resposta" class="form-label fw-semibold">Resposta <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('resposta') is-invalid @enderror"
                                      id="resposta"
                                      name="resposta"
                                      rows="6"
                                      placeholder="Digite a resposta completa..."
                                      required>{{ old('resposta', $faq?->resposta) }}</textarea>
                            @error('resposta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Quebras de linha serão preservadas na exibição.</div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="ordem" class="form-label fw-semibold">Ordem de exibição</label>
                                <input type="number"
                                       class="form-control @error('ordem') is-invalid @enderror"
                                       id="ordem"
                                       name="ordem"
                                       value="{{ old('ordem', $faq?->ordem ?? 0) }}"
                                       min="0">
                                @error('ordem') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Menor número aparece primeiro.</div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check mb-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="ativo"
                                           value="1"
                                           id="ativo"
                                           {{ old('ativo', $faq?->ativo ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="ativo">
                                        <i class="bi bi-eye me-1 text-success"></i>Ativo (visível no site)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-pm">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ $faq ? 'Salvar Alterações' : 'Cadastrar Pergunta' }}
                            </button>
                            <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

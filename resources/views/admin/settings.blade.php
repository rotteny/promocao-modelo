@extends('layouts.admin')

@section('title', 'Configurações - Admin')
@section('page-title', 'Configurações')

@push('styles')
<style>
    .setting-group {
        border-left: 4px solid var(--pm-primary);
        background: #fff;
        border-radius: 0 12px 12px 0;
    }
    .setting-group .card-header {
        background: transparent;
        border-bottom: 1px solid #eee;
    }
    .setting-group .card-header h6 {
        color: var(--pm-primary);
    }
    .form-label .badge {
        font-weight: 500;
        font-size: 0.65rem;
        vertical-align: middle;
    }
    .setting-hint {
        font-size: 0.78rem;
        color: #6c757d;
        margin-top: 0.2rem;
    }
</style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-gear me-2"></i>Configurações</h2>
                    <p class="text-muted mb-0">Gerencie as regras e parâmetros da promoção</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
                @csrf

                @php
                    // Indexa settings por chave para acesso fácil
                    $s = $settings->keyBy('key');
                    $idx = 0;
                @endphp

                {{-- ================================================
                     PROMOÇÃO
                     ================================================ --}}
                <div class="card setting-group shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-megaphone me-2"></i>Promoção</h6>
                    </div>
                    <div class="card-body p-4">
                        {{-- Nome da Promoção --}}
                        @if($s->has('nome_promocao'))
                        <div class="mb-4">
                            <label for="setting_nome_promocao" class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i>Nome da Promoção
                            </label>
                            <input type="hidden" name="settings[{{ $idx }}][key]" value="nome_promocao">
                            <input type="text" class="form-control" id="setting_nome_promocao"
                                   name="settings[{{ $idx }}][value]" value="{{ $s->get('nome_promocao')->value }}"
                                   placeholder="Ex: Promoção Modelo 2025" maxlength="255">
                            <div class="setting-hint">{{ $s->get('nome_promocao')->description }}</div>
                        </div>
                        @php $idx++; @endphp
                        @endif

                        {{-- Data e Hora de Início --}}
                        @if($s->has('data_inicio'))
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="setting_data_inicio" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-1"></i>Data e Hora de Início
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="data_inicio">
                                <input type="datetime-local" class="form-control" id="setting_data_inicio"
                                       name="settings[{{ $idx }}][value]"
                                       value="{{ \Carbon\Carbon::parse($s->get('data_inicio')->value)->format('Y-m-d\TH:i') }}">
                                <div class="setting-hint">Cadastros e cupons serão aceitos a partir desta data/hora.</div>
                            </div>
                            @php $idx++; @endphp

                            {{-- Data e Hora de Término --}}
                            @if($s->has('data_fim'))
                            <div class="col-md-6">
                                <label for="setting_data_fim" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-x me-1"></i>Data e Hora de Término
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="data_fim">
                                <input type="datetime-local" class="form-control" id="setting_data_fim"
                                       name="settings[{{ $idx }}][value]"
                                       value="{{ \Carbon\Carbon::parse($s->get('data_fim')->value)->format('Y-m-d\TH:i') }}">
                                <div class="setting-hint">Novos cadastros e cupons serão bloqueados após esta data/hora.</div>
                            </div>
                            @php $idx++; @endphp
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ================================================
                     NÚMEROS DA SORTE
                     ================================================ --}}
                <div class="card setting-group shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-stars me-2"></i>Números da Sorte</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            {{-- Valor por Número --}}
                            @if($s->has('valor_por_numero'))
                            <div class="col-md-4">
                                <label for="setting_valor_por_numero" class="form-label fw-semibold">
                                    <i class="bi bi-currency-dollar me-1"></i>Valor por Número (R$)
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="valor_por_numero">
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="setting_valor_por_numero"
                                           name="settings[{{ $idx }}][value]" value="{{ $s->get('valor_por_numero')->value }}"
                                           min="1" step="1">
                                </div>
                                <div class="setting-hint">A cada R$ X em compras = 1 número da sorte.</div>
                            </div>
                            @php $idx++; @endphp
                            @endif

                            {{-- Tipo de Bônus --}}
                            @if($s->has('bonus_numeros'))
                            <div class="col-md-4">
                                <label for="setting_bonus_numeros" class="form-label fw-semibold">
                                    <i class="bi bi-gift me-1"></i>Regra de Bônus
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="bonus_numeros">
                                <select class="form-select" id="setting_bonus_numeros" name="settings[{{ $idx }}][value]">
                                    <option value="proporcional" {{ $s->get('bonus_numeros')->value === 'proporcional' ? 'selected' : '' }}>
                                        Proporcional (+1 a cada R$ X em bônus)
                                    </option>
                                    <option value="desativado" {{ $s->get('bonus_numeros')->value === 'desativado' ? 'selected' : '' }}>
                                        Desativado (sem números bônus)
                                    </option>
                                </select>
                                <div class="setting-hint">Define como produtos bônus geram números extras.</div>
                            </div>
                            @php $idx++; @endphp
                            @endif

                            {{-- Total de Séries --}}
                            @if($s->has('total_series'))
                            <div class="col-md-2">
                                <label for="setting_total_series" class="form-label fw-semibold">
                                    Séries
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="total_series">
                                <input type="number" class="form-control" id="setting_total_series"
                                       name="settings[{{ $idx }}][value]" value="{{ $s->get('total_series')->value }}"
                                       min="1" max="99" step="1">
                                <div class="setting-hint">Total de séries.</div>
                            </div>
                            @php $idx++; @endphp
                            @endif

                            {{-- Números por Série --}}
                            @if($s->has('numeros_por_serie'))
                            <div class="col-md-2">
                                <label for="setting_numeros_por_serie" class="form-label fw-semibold">
                                    Nº/Série
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="numeros_por_serie">
                                <input type="number" class="form-control" id="setting_numeros_por_serie"
                                       name="settings[{{ $idx }}][value]" value="{{ $s->get('numeros_por_serie')->value }}"
                                       min="100" max="999999" step="1">
                                <div class="setting-hint">Números por série.</div>
                            </div>
                            @php $idx++; @endphp
                            @endif
                        </div>

                        {{-- Resumo da Capacidade --}}
                        @php
                            $series = (int) ($s->get('total_series')?->value ?? 10);
                            $porSerie = (int) ($s->get('numeros_por_serie')?->value ?? 10000);
                            $capacidade = $series * $porSerie;
                        @endphp
                        <div class="alert alert-info py-2 mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Capacidade total:</strong>
                            {{ $series }} séries &times; {{ number_format($porSerie) }} números =
                            <strong>{{ number_format($capacidade) }} números da sorte</strong>
                        </div>
                    </div>
                </div>

                {{-- ================================================
                     CONTROLE DA CAMPANHA
                     ================================================ --}}
                <div class="card setting-group shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2"></i>Controle da Campanha</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            {{-- Campanha Encerrada --}}
                            @if($s->has('campanha_encerrada'))
                            <div class="col-md-4">
                                <label for="setting_campanha_encerrada" class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on me-1"></i>Status da Campanha
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="campanha_encerrada">
                                <select class="form-select" id="setting_campanha_encerrada" name="settings[{{ $idx }}][value]">
                                    <option value="false" {{ $s->get('campanha_encerrada')->value !== 'true' ? 'selected' : '' }}>
                                        Ativa / Normal
                                    </option>
                                    <option value="true" {{ $s->get('campanha_encerrada')->value === 'true' ? 'selected' : '' }}>
                                        Encerrada
                                    </option>
                                </select>
                                <div class="setting-hint">Encerra ou reabre a campanha manualmente.</div>
                            </div>
                            @php $idx++; @endphp
                            @endif

                            {{-- Motivo de Encerramento --}}
                            @if($s->has('campanha_motivo_encerramento'))
                            <div class="col-md-4">
                                <label for="setting_motivo_encerramento" class="form-label fw-semibold">
                                    <i class="bi bi-chat-text me-1"></i>Motivo do Encerramento
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="campanha_motivo_encerramento">
                                <select class="form-select" id="setting_motivo_encerramento" name="settings[{{ $idx }}][value]">
                                    <option value="" {{ $s->get('campanha_motivo_encerramento')->value === '' ? 'selected' : '' }}>
                                        (nenhum)
                                    </option>
                                    <option value="manual" {{ $s->get('campanha_motivo_encerramento')->value === 'manual' ? 'selected' : '' }}>
                                        Manual (encerrada pelo administrador)
                                    </option>
                                    <option value="esgotamento" {{ $s->get('campanha_motivo_encerramento')->value === 'esgotamento' ? 'selected' : '' }}>
                                        Esgotamento (todos os números distribuídos)
                                    </option>
                                </select>
                                <div class="setting-hint">Preenchido automaticamente ou pelo admin.</div>
                            </div>
                            @php $idx++; @endphp
                            @endif
                        </div>

                        {{-- Ações rápidas de campanha --}}
                        @php
                            $campanhaEncerradaVal = $s->get('campanha_encerrada')?->value === 'true';
                            $adminAtual = Auth::guard('admin')->user();
                        @endphp
                        @if($adminAtual->temPermissao('perm_encerrar_campanha'))
                        <hr class="my-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <span class="fw-semibold"><i class="bi bi-lightning me-1"></i>Ação Rápida:</span>
                                <span class="text-muted">Encerrar ou reabrir a campanha com um clique.</span>
                            </div>
                            @if(! $campanhaEncerradaVal)
                                <form action="{{ route('admin.campanha.encerrar') }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja ENCERRAR a campanha? Novos cadastros e cupons serão bloqueados imediatamente.')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-stop-circle me-1"></i>Encerrar Campanha
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.campanha.reabrir') }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja REABRIR a campanha? Cadastros e cupons serão liberados novamente.')">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-play-circle me-1"></i>Reabrir Campanha
                                    </button>
                                </form>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ================================================
                     FILA DE PROCESSAMENTO
                     ================================================ --}}
                <div class="card setting-group shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-cpu me-2"></i>Fila de Processamento</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Fila Bloqueada --}}
                            @if($s->has('fila_bloqueada'))
                            <div class="col-md-4">
                                <label for="setting_fila_bloqueada" class="form-label fw-semibold">
                                    <i class="bi bi-lock me-1"></i>Status da Fila
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="fila_bloqueada">
                                <select class="form-select {{ $s->get('fila_bloqueada')->value === 'true' ? 'border-danger text-danger' : '' }}"
                                        id="setting_fila_bloqueada" name="settings[{{ $idx }}][value]">
                                    <option value="false" {{ $s->get('fila_bloqueada')->value !== 'true' ? 'selected' : '' }}>
                                        Desbloqueada (processando normalmente)
                                    </option>
                                    <option value="true" {{ $s->get('fila_bloqueada')->value === 'true' ? 'selected' : '' }}>
                                        Bloqueada (processamento parado)
                                    </option>
                                </select>
                                <div class="setting-hint">Controla se novos cupons são processados.</div>
                            </div>
                            @php $idx++; @endphp
                            @endif

                            {{-- Cupom com Erro --}}
                            @if($s->has('fila_cupom_erro_id'))
                            <div class="col-md-4">
                                <label for="setting_fila_cupom_erro_id" class="form-label fw-semibold">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Cupom com Erro
                                    <span class="badge bg-secondary">Sistema</span>
                                </label>
                                <input type="hidden" name="settings[{{ $idx }}][key]" value="fila_cupom_erro_id">
                                <input type="text" class="form-control bg-light" id="setting_fila_cupom_erro_id"
                                       name="settings[{{ $idx }}][value]" value="{{ $s->get('fila_cupom_erro_id')->value }}"
                                       readonly>
                                <div class="setting-hint">ID do cupom que causou o bloqueio (gerenciado automaticamente).</div>
                            </div>
                            @php $idx++; @endphp
                            @endif
                        </div>

                        @if($s->has('fila_bloqueada') && $s->get('fila_bloqueada')->value === 'true')
                            <div class="alert alert-danger mt-3 mb-0 py-2">
                                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                                <strong>Atenção:</strong> A fila está bloqueada. Nenhum cupom será processado até que seja desbloqueada.
                                Use o painel principal para reprocessar o cupom com erro e desbloquear.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Botão Salvar --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-pm btn-lg">
                        <i class="bi bi-check-circle me-2"></i>Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação visual: data fim deve ser posterior à data início
    const dataInicio = document.getElementById('setting_data_inicio');
    const dataFim = document.getElementById('setting_data_fim');

    function validarDatas() {
        if (dataInicio && dataFim && dataInicio.value && dataFim.value) {
            if (dataFim.value <= dataInicio.value) {
                dataFim.classList.add('is-invalid');
                dataFim.setCustomValidity('A data de término deve ser posterior à data de início.');
            } else {
                dataFim.classList.remove('is-invalid');
                dataFim.setCustomValidity('');
            }
        }
    }

    if (dataInicio) dataInicio.addEventListener('change', validarDatas);
    if (dataFim) dataFim.addEventListener('change', validarDatas);

    // Atualiza resumo de capacidade ao alterar séries ou números por série
    const totalSeries = document.getElementById('setting_total_series');
    const numPorSerie = document.getElementById('setting_numeros_por_serie');

    function atualizarCapacidade() {
        const series = parseInt(totalSeries?.value) || 0;
        const porSerie = parseInt(numPorSerie?.value) || 0;
        const capacidade = series * porSerie;

        const alerta = document.querySelector('.alert-info strong:last-child');
        if (alerta) {
            alerta.textContent = capacidade.toLocaleString('pt-BR') + ' números da sorte';
        }
    }

    if (totalSeries) totalSeries.addEventListener('input', atualizarCapacidade);
    if (numPorSerie) numPorSerie.addEventListener('input', atualizarCapacidade);

    // Interação: motivo de encerramento depende do status da campanha
    const campanhaEncerrada = document.getElementById('setting_campanha_encerrada');
    const motivoEncerramento = document.getElementById('setting_motivo_encerramento');

    function toggleMotivo() {
        if (campanhaEncerrada && motivoEncerramento) {
            if (campanhaEncerrada.value === 'false') {
                motivoEncerramento.value = '';
                motivoEncerramento.setAttribute('disabled', 'disabled');
            } else {
                motivoEncerramento.removeAttribute('disabled');
                if (!motivoEncerramento.value) {
                    motivoEncerramento.value = 'manual';
                }
            }
        }
    }

    if (campanhaEncerrada) {
        campanhaEncerrada.addEventListener('change', toggleMotivo);
        toggleMotivo();
    }

    // Garante que campos disabled são enviados (cria hidden)
    document.getElementById('settingsForm')?.addEventListener('submit', function() {
        if (motivoEncerramento && motivoEncerramento.disabled) {
            motivoEncerramento.removeAttribute('disabled');
        }
    });
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Cadastrar Cupom - Promoção Modelo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-pm-gradient text-white py-3">
                    <h4 class="mb-0"><i class="bi bi-receipt me-2"></i>Cadastrar Cupom Fiscal</h4>
                </div>
                <div class="card-body p-4">
                    <!-- Aviso de promoção fictícia -->
                    <div class="alert alert-warning py-2 mb-3">
                        <small>
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Promoção fictícia</strong> — Projeto de demonstração. Os cupons cadastrados não possuem validade real.
                        </small>
                    </div>

                    <!-- Alerta de promoção encerrada (oculto, exibido via JS) -->
                    <div id="alertaPromocaoEncerrada" class="alert alert-danger d-none" role="alert">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i>
                        <strong>Promoção encerrada!</strong>
                        <span id="alertaMensagem">O período da promoção foi encerrado.</span>
                        <div class="mt-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-danger">
                                <i class="bi bi-arrow-left me-1"></i>Voltar ao Dashboard
                            </a>
                        </div>
                    </div>

                    {{-- ===== ETAPA 1: Escolha do método de cadastro ===== --}}
                    <div id="etapaEscolha">
                        <div class="text-center mb-4">
                            <h5 class="fw-bold text-muted">Como deseja cadastrar seu cupom?</h5>
                            <p class="text-muted small">Escolha uma das opções abaixo para registrar seu cupom fiscal</p>
                        </div>
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-5">
                                <div class="card border-2 h-100 text-center p-4 cursor-pointer opcao-cadastro" id="opcaoQrCode" role="button" tabindex="0">
                                    <div class="card-body">
                                        <div class="bg-pm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="bi bi-qr-code-scan text-white" style="font-size: 2rem;"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">Leitura por QR Code</h5>
                                        <p class="text-muted small mb-0">
                                            Use a câmera do seu dispositivo para ler o QR Code da nota fiscal.
                                            Os dados serão preenchidos automaticamente.
                                        </p>
                                    </div>
                                    <div class="card-footer bg-transparent border-0">
                                        <span class="badge bg-success"><i class="bi bi-lightning-fill me-1"></i>Mais rápido</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card border-2 h-100 text-center p-4 cursor-pointer opcao-cadastro" id="opcaoManual" role="button" tabindex="0">
                                    <div class="card-body">
                                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="bi bi-pencil-square text-white" style="font-size: 2rem;"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">Cadastro Manual</h5>
                                        <p class="text-muted small mb-0">
                                            Preencha os dados do cupom fiscal manualmente
                                            com as informações impressas na nota.
                                        </p>
                                    </div>
                                    <div class="card-footer bg-transparent border-0">
                                        <span class="badge bg-secondary"><i class="bi bi-keyboard me-1"></i>Digitação</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== ETAPA 2: Scanner de QR Code ===== --}}
                    <div id="etapaScanner" class="d-none">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold text-muted">
                                <i class="bi bi-qr-code-scan me-2"></i>Escaneie o QR Code da Nota Fiscal
                            </h5>
                            <p class="text-muted small">Aponte a câmera para o QR Code impresso no cupom fiscal</p>
                        </div>

                        <!-- Área do scanner -->
                        <div class="mx-auto mb-3" style="max-width: 400px;">
                            <div id="scannerContainer" class="position-relative bg-dark rounded overflow-hidden" style="aspect-ratio: 1;">
                                <video id="scannerVideo" class="w-100 h-100" style="object-fit: cover;" playsinline></video>
                                <canvas id="scannerCanvas" class="d-none"></canvas>
                                <!-- Overlay com guia de posicionamento -->
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="pointer-events: none;">
                                    <div style="width: 70%; height: 70%; border: 3px solid rgba(255,255,255,0.7); border-radius: 12px;"></div>
                                </div>
                                <!-- Indicador de carregamento -->
                                <div id="scannerLoading" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark">
                                    <div class="text-center text-white">
                                        <div class="spinner-border mb-2" role="status"></div>
                                        <p class="mb-0 small">Iniciando câmera...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status do scanner -->
                        <div id="scannerStatus" class="text-center mb-3">
                            <span class="badge bg-info fs-6 px-3 py-2">
                                <i class="bi bi-camera-video me-1"></i>Procurando QR Code...
                            </span>
                        </div>

                        <!-- Alerta de consulta Sefaz -->
                        <div id="alertaConsultaSefaz" class="alert alert-info d-none text-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            <strong>QR Code detectado!</strong> Consultando dados na Sefaz...
                        </div>

                        <!-- Alerta de erro no scanner -->
                        <div id="alertaErroScanner" class="alert alert-warning d-none">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Não foi possível ler o cupom.</strong>
                            <span id="erroScannerMensagem"></span>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-warning me-2" id="btnTentarNovamente">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Tentar Novamente
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCadastroManualFromScanner">
                                    <i class="bi bi-pencil-square me-1"></i>Cadastro Manual
                                </button>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-outline-secondary" id="btnVoltarEscolha">
                                <i class="bi bi-arrow-left me-1"></i>Voltar
                            </button>
                        </div>
                    </div>

                    {{-- ===== ETAPA 3: Formulário de Cadastro ===== --}}
                    <div id="etapaFormulario" class="d-none">
                        <!-- Indicador de origem (QR Code ou Manual) -->
                        <div id="origemQrCode" class="alert alert-success d-none mb-3">
                            <i class="bi bi-qr-code-scan me-2"></i>
                            <strong>Dados obtidos via QR Code.</strong> Confira as informações e adicione ou ajuste os itens se necessário.
                        </div>

                        <form method="POST" action="{{ route('cupom.store') }}" id="formCupom">
                            @csrf
                            <input type="hidden" name="chave_acesso" id="chave_acesso" value="{{ old('chave_acesso') }}">

                            <!-- Dados do Cupom -->
                            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-upc-scan me-1"></i>Dados do Cupom</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="numero" class="form-label">Número do Cupom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                           id="numero" name="numero" value="{{ old('numero') }}"
                                           inputmode="numeric" pattern="[0-9]*" placeholder="Somente números"
                                           oninput="this.value = this.value.replace(/\D/g, '')" required>
                                    @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="cnpj_loja" class="form-label">CNPJ da Loja <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('cnpj_loja') is-invalid @enderror"
                                           id="cnpj_loja" name="cnpj_loja" value="{{ old('cnpj_loja') }}"
                                           inputmode="numeric" maxlength="18" placeholder="00.000.000/0000-00"
                                           required>
                                    @error('cnpj_loja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="data_compra" class="form-label">Data da Compra <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('data_compra') is-invalid @enderror"
                                           id="data_compra" name="data_compra" value="{{ old('data_compra', date('Y-m-d')) }}"
                                           min="{{ $promocao->getDataInicio()->toDateString() }}"
                                           max="{{ min($promocao->getDataFim()->toDateString(), date('Y-m-d')) }}" required>
                                    <div class="form-text small">Entre {{ $promocao->getDataInicio()->format('d/m/Y') }} e hoje</div>
                                    @error('data_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Itens do Cupom -->
                            <h6 class="fw-bold text-muted mb-3">
                                <i class="bi bi-list-check me-1"></i>Itens do Cupom
                                <button type="button" class="btn btn-sm btn-outline-success ms-2" id="btnAddItem">
                                    <i class="bi bi-plus-circle me-1"></i>Adicionar Item
                                </button>
                            </h6>

                            <div id="itensContainer">
                                <!-- Item Template -->
                                <div class="item-row card bg-light mb-2 p-3" data-index="0">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-5">
                                            <label class="form-label small fw-semibold">Produto <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" name="itens[0][produto_participante_id]" required>
                                                <option value="">Selecione...</option>
                                                @foreach($produtos as $produto)
                                                    <option value="{{ $produto->id }}" {{ $produto->bonus ? 'data-bonus=true' : '' }}>
                                                        {{ $produto->descricao }}
                                                        @if($produto->bonus) ★ BÔNUS @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold">Qtd <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control form-control-sm item-qtd"
                                                   name="itens[0][quantidade]" min="1" value="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold">Valor Unit. <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control item-valor"
                                                       name="itens[0][valor_unitario]" step="0.01" min="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold">Subtotal</label>
                                            <div class="form-control form-control-sm bg-white item-subtotal fw-bold">R$ 0,00</div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" title="Remover item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumo -->
                            <div class="card bg-dark text-white p-3 mt-3 mb-2">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <span class="fw-bold"><i class="bi bi-calculator me-2"></i>Valor Total:</span>
                                        <span class="fs-4 fw-bold ms-2" id="valorTotal">R$ 0,00</span>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-success fs-6" id="badgeNumeros">
                                            <i class="bi bi-stars me-1"></i>0 número(s) da sorte
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div id="alertaValorMinimo" class="alert alert-warning py-2 d-none mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                O valor mínimo em produtos para cadastrar um cupom é de <strong>R$ 20,00</strong>.
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-lg" id="btnVoltarEscolhaFromForm">
                                    <i class="bi bi-arrow-left me-1"></i>Voltar
                                </button>
                                <button type="submit" class="btn btn-pm btn-lg flex-grow-1">
                                    <i class="bi bi-check-circle me-2"></i>Cadastrar Cupom
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .opcao-cadastro {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .opcao-cadastro:hover, .opcao-cadastro:focus {
        border-color: var(--bs-primary) !important;
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    #scannerVideo {
        transform: scaleX(-1); /* Espelha para parecer espelho */
    }
</style>
@endpush

@push('scripts')
<!-- html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === Elementos ===
    const etapaEscolha = document.getElementById('etapaEscolha');
    const etapaScanner = document.getElementById('etapaScanner');
    const etapaFormulario = document.getElementById('etapaFormulario');
    const origemQrCode = document.getElementById('origemQrCode');
    const alertaConsultaSefaz = document.getElementById('alertaConsultaSefaz');
    const alertaErroScanner = document.getElementById('alertaErroScanner');
    const erroScannerMensagem = document.getElementById('erroScannerMensagem');
    const scannerLoading = document.getElementById('scannerLoading');
    const scannerStatus = document.getElementById('scannerStatus');

    let html5QrCode = null;
    let itemIndex = 1;
    const valorPorNumero = 20;
    const consultarQrCodeUrl = '{{ route("cupom.consultar-qrcode") }}';
    const csrfToken = '{{ csrf_token() }}';

    // === Máscara CNPJ ===
    const cnpjInput = document.getElementById('cnpj_loja');
    cnpjInput.addEventListener('input', function() {
        formatarCnpj(this);
    });

    function formatarCnpj(el) {
        let v = el.value.replace(/\D/g, '').substring(0, 14);
        if (v.length > 12) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/, '$1.$2.$3/$4-$5');
        else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{1,4})/, '$1.$2.$3/$4');
        else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{1,3})/, '$1.$2.$3');
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{1,3})/, '$1.$2');
        el.value = v;
    }

    // Ao submeter, envia apenas os 14 dígitos
    document.getElementById('formCupom').addEventListener('submit', function() {
        cnpjInput.value = cnpjInput.value.replace(/\D/g, '');
    });

    // === Navegação entre etapas ===
    function mostrarEtapa(etapa) {
        etapaEscolha.classList.add('d-none');
        etapaScanner.classList.add('d-none');
        etapaFormulario.classList.add('d-none');
        etapa.classList.remove('d-none');
    }

    // === Opção QR Code ===
    document.getElementById('opcaoQrCode').addEventListener('click', function() {
        mostrarEtapa(etapaScanner);
        iniciarScanner();
    });

    // === Opção Manual ===
    document.getElementById('opcaoManual').addEventListener('click', function() {
        mostrarEtapa(etapaFormulario);
        origemQrCode.classList.add('d-none');
    });

    // === Voltar à escolha ===
    document.getElementById('btnVoltarEscolha').addEventListener('click', function() {
        pararScanner();
        mostrarEtapa(etapaEscolha);
    });

    document.getElementById('btnVoltarEscolhaFromForm').addEventListener('click', function() {
        mostrarEtapa(etapaEscolha);
    });

    // === Tentar novamente o scanner ===
    document.getElementById('btnTentarNovamente').addEventListener('click', function() {
        alertaErroScanner.classList.add('d-none');
        alertaConsultaSefaz.classList.add('d-none');
        scannerStatus.classList.remove('d-none');
        iniciarScanner();
    });

    // === Ir para manual a partir do scanner ===
    document.getElementById('btnCadastroManualFromScanner').addEventListener('click', function() {
        pararScanner();
        mostrarEtapa(etapaFormulario);
        origemQrCode.classList.add('d-none');
    });

    // === Scanner de QR Code ===
    function iniciarScanner() {
        scannerLoading.classList.remove('d-none');
        alertaErroScanner.classList.add('d-none');
        alertaConsultaSefaz.classList.add('d-none');
        scannerStatus.classList.remove('d-none');

        // Se já existe uma instância, para antes
        if (html5QrCode) {
            html5QrCode.stop().catch(() => {}).finally(() => {
                criarScanner();
            });
        } else {
            criarScanner();
        }
    }

    function criarScanner() {
        html5QrCode = new Html5Qrcode("scannerContainer", { verbose: false });

        const config = {
            fps: 10,
            qrbox: function(viewfinderWidth, viewfinderHeight) {
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                const size = Math.floor(minEdge * 0.7);
                return { width: size, height: size };
            },
            aspectRatio: 1.0,
        };

        html5QrCode.start(
            { facingMode: "environment" }, // Câmera traseira
            config,
            onQrCodeSuccess,
            onQrCodeError
        ).then(() => {
            scannerLoading.classList.add('d-none');
        }).catch((err) => {
            scannerLoading.classList.add('d-none');
            scannerStatus.classList.add('d-none');
            mostrarErroScanner('Não foi possível acessar a câmera. Verifique as permissões do navegador. (' + err + ')');
        });
    }

    function pararScanner() {
        if (html5QrCode) {
            html5QrCode.stop().catch(() => {});
            html5QrCode = null;
        }
        // Limpa o container do scanner para evitar elementos residuais
        const container = document.getElementById('scannerContainer');
        if (container) {
            // Mantém apenas o overlay de loading e guia
            const video = document.getElementById('scannerVideo');
            const canvas = document.getElementById('scannerCanvas');
            // html5-qrcode cria seus próprios elementos, deixar ele limpar
        }
    }

    function onQrCodeSuccess(decodedText) {
        // QR Code detectado - parar scanner e consultar Sefaz
        pararScanner();
        scannerStatus.classList.add('d-none');
        alertaConsultaSefaz.classList.remove('d-none');
        consultarSefaz(decodedText);
    }

    function onQrCodeError(errorMessage) {
        // Erro normal de leitura (frame sem QR) - ignorar silenciosamente
    }

    function mostrarErroScanner(mensagem) {
        erroScannerMensagem.textContent = mensagem;
        alertaErroScanner.classList.remove('d-none');
    }

    // === Consulta à Sefaz ===
    function consultarSefaz(qrCodeData) {
        fetch(consultarQrCodeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ qrcode_data: qrCodeData }),
        })
        .then(response => response.json())
        .then(data => {
            alertaConsultaSefaz.classList.add('d-none');

            if (data.success) {
                // Sucesso: preencher formulário com dados da nota
                preencherFormularioComDadosSefaz(data.data);
                mostrarEtapa(etapaFormulario);
                origemQrCode.classList.remove('d-none');
            } else {
                // Falha: mostrar mensagem e oferecer cadastro manual
                mostrarErroScanner(data.message);
            }
        })
        .catch(error => {
            alertaConsultaSefaz.classList.add('d-none');
            mostrarErroScanner('Ocorreu um erro ao consultar os dados do cupom. Tente novamente ou faça o cadastro manual.');
        });
    }

    // === Preencher formulário com dados da Sefaz ===
    function preencherFormularioComDadosSefaz(dados) {
        // Preenche campos básicos
        document.getElementById('chave_acesso').value = dados.chave_acesso || '';
        document.getElementById('numero').value = dados.numero || '';
        document.getElementById('cnpj_loja').value = dados.cnpj_loja || '';
        formatarCnpj(document.getElementById('cnpj_loja'));
        document.getElementById('data_compra').value = dados.data_compra || '';

        // Remove todos os itens existentes exceto o template
        const container = document.getElementById('itensContainer');
        container.innerHTML = '';

        // Adiciona itens dos produtos participantes encontrados
        if (dados.produtos_participantes && dados.produtos_participantes.length > 0) {
            dados.produtos_participantes.forEach((item, index) => {
                adicionarItemPreenchido(index, item.produto_participante_id, item.quantidade, item.valor_unitario);
            });
            itemIndex = dados.produtos_participantes.length;
        } else {
            // Se não há itens, adiciona um vazio
            adicionarItemVazio(0);
            itemIndex = 1;
        }

        calcularTotal();
    }

    function adicionarItemPreenchido(index, produtoId, quantidade, valorUnitario) {
        const container = document.getElementById('itensContainer');
        const produtosOptions = gerarProdutosOptions(produtoId);

        const html = `
            <div class="item-row card bg-light mb-2 p-3" data-index="${index}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Produto <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="itens[${index}][produto_participante_id]" required>
                            ${produtosOptions}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Qtd <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm item-qtd"
                               name="itens[${index}][quantidade]" min="1" value="${quantidade}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Valor Unit. <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control item-valor"
                                   name="itens[${index}][valor_unitario]" step="0.01" min="0.01" value="${valorUnitario}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Subtotal</label>
                        <div class="form-control form-control-sm bg-white item-subtotal fw-bold">R$ 0,00</div>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" title="Remover item">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>`;

        container.insertAdjacentHTML('beforeend', html);
    }

    function adicionarItemVazio(index) {
        adicionarItemPreenchido(index, '', 1, '');
    }

    function gerarProdutosOptions(selectedId) {
        const produtos = @json($produtos);
        let options = '<option value="">Selecione...</option>';
        produtos.forEach(p => {
            const selected = p.id == selectedId ? 'selected' : '';
            const bonus = p.bonus ? 'data-bonus="true"' : '';
            const label = p.descricao + (p.bonus ? ' ★ BÔNUS' : '');
            options += `<option value="${p.id}" ${bonus} ${selected}>${label}</option>`;
        });
        return options;
    }

    // === Gestão de Itens (Manual) ===
    document.getElementById('btnAddItem').addEventListener('click', function() {
        adicionarItemVazio(itemIndex);
        itemIndex++;
    });

    document.getElementById('itensContainer').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-item')) {
            const row = e.target.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calcularTotal();
            }
        }
    });

    document.getElementById('itensContainer').addEventListener('input', calcularTotal);
    document.getElementById('itensContainer').addEventListener('change', calcularTotal);

    // === Cálculo de Total e Números da Sorte ===
    const VALOR_MINIMO_CUPOM = 20;

    function calcularTotal() {
        let total = 0;
        let totalBonus = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const qtd = parseFloat(row.querySelector('.item-qtd')?.value) || 0;
            const valor = parseFloat(row.querySelector('.item-valor')?.value) || 0;
            const subtotal = qtd * valor;
            total += subtotal;

            const subtotalEl = row.querySelector('.item-subtotal');
            if (subtotalEl) {
                subtotalEl.textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
            }

            const select = row.querySelector('select');
            const selected = select?.options[select.selectedIndex];
            if (selected && selected.dataset.bonus === 'true') {
                totalBonus += subtotal;
            }
        });

        const numerosBase = Math.floor(total / valorPorNumero);
        const numerosBonus = Math.floor(totalBonus / valorPorNumero);
        const numeros = numerosBase + numerosBonus;

        document.getElementById('valorTotal').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
        document.getElementById('badgeNumeros').innerHTML = `<i class="bi bi-stars me-1"></i>${numeros} número(s) da sorte`;

        if (numerosBonus > 0) {
            document.getElementById('badgeNumeros').innerHTML += ` <span class="badge bg-warning text-dark ms-1">+${numerosBonus} bônus</span>`;
        }

        // Validação de valor mínimo
        const alertaMin = document.getElementById('alertaValorMinimo');
        const btnSubmit = document.querySelector('#formCupom button[type="submit"]');
        if (total > 0 && total < VALOR_MINIMO_CUPOM) {
            alertaMin.classList.remove('d-none');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50');
        } else {
            alertaMin.classList.add('d-none');
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('opacity-50');
        }
    }

    // === Verificação de status da promoção ===
    const statusUrl = '{{ route("api.promocao.status") }}';
    let promocaoAtiva = true;

    function verificarStatusPromocao() {
        fetch(statusUrl)
            .then(r => r.json())
            .then(data => {
                if (!data.ativa) {
                    promocaoAtiva = false;
                    const alerta = document.getElementById('alertaPromocaoEncerrada');
                    const msg = document.getElementById('alertaMensagem');
                    if (alerta) {
                        alerta.classList.remove('d-none');
                        msg.textContent = data.mensagem;
                    }
                    const form = document.getElementById('formCupom');
                    if (form) {
                        form.querySelectorAll('input, select, button[type="submit"]').forEach(el => {
                            el.disabled = true;
                        });
                    }
                    // Desabilita também os botões de escolha
                    document.querySelectorAll('.opcao-cadastro').forEach(el => {
                        el.style.pointerEvents = 'none';
                        el.style.opacity = '0.5';
                    });
                }
            })
            .catch(() => {});
    }

    setInterval(verificarStatusPromocao, 30000);

    document.getElementById('formCupom').addEventListener('submit', function(e) {
        if (!promocaoAtiva) {
            e.preventDefault();
            alert('A promoção foi encerrada. Não é possível cadastrar novos cupons.');
            return false;
        }

        // Validação de valor mínimo no submit
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qtd = parseFloat(row.querySelector('.item-qtd')?.value) || 0;
            const valor = parseFloat(row.querySelector('.item-valor')?.value) || 0;
            total += qtd * valor;
        });
        if (total < VALOR_MINIMO_CUPOM) {
            e.preventDefault();
            alert('O valor total dos produtos deve ser de no mínimo R$ 20,00.');
            return false;
        }
    });

    // === Se há erros de validação (old), ir direto ao formulário ===
    @if($errors->any())
        mostrarEtapa(etapaFormulario);
    @endif
});
</script>
@endpush

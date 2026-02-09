@extends('layouts.app')

@section('title', 'Cadastro - Promoção Modelo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-pm-gradient text-white text-center py-3">
                    <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Cadastre-se na Promoção</h4>
                </div>
                <div class="card-body p-4">
                    <!-- Aviso de promoção fictícia -->
                    <div class="alert alert-warning py-2 mb-3">
                        <small>
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Promoção fictícia</strong> — Projeto de demonstração. Nenhum dado possui validade real.
                        </small>
                    </div>

                    <!-- Alerta de promoção encerrada (oculto, exibido via JS) -->
                    <div id="alertaPromocaoEncerrada" class="alert alert-danger d-none" role="alert">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i>
                        <strong>Promoção encerrada!</strong>
                        <span id="alertaMensagem">O período da promoção foi encerrado.</span>
                        <div class="mt-2">
                            <a href="{{ route('home') }}" class="btn btn-sm btn-danger">
                                <i class="bi bi-arrow-left me-1"></i>Voltar à Página Inicial
                            </a>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="formRegistro">
                        @csrf

                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person me-1"></i>Dados Pessoais</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                       id="cpf" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                                       maxlength="14" required>
                                @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                       id="telefone" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 0000-0000">
                                @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="celular" class="form-label">Celular</label>
                                <input type="text" class="form-control @error('celular') is-invalid @enderror"
                                       id="celular" name="celular" value="{{ old('celular') }}" placeholder="(00) 00000-0000">
                                @error('celular') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-geo-alt me-1"></i>Endereço</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                       id="cep" name="cep" value="{{ old('cep') }}" placeholder="00000-000" maxlength="10">
                                @error('cep') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                                       id="endereco" name="endereco" value="{{ old('endereco') }}">
                                @error('endereco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                       id="numero" name="numero" value="{{ old('numero') }}">
                                @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control @error('complemento') is-invalid @enderror"
                                       id="complemento" name="complemento" value="{{ old('complemento') }}">
                                @error('complemento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                       id="bairro" name="bairro" value="{{ old('bairro') }}">
                                @error('bairro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                                       id="cidade" name="cidade" value="{{ old('cidade') }}">
                                @error('cidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="estado" class="form-label">UF</label>
                                <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                                    <option value="">UF</option>
                                    @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                        <option value="{{ $uf }}" {{ old('estado') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                                @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-lock me-1"></i>Segurança</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required minlength="8">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                       name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-pm btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Criar Minha Conta
                            </button>
                        </div>
                    </form>

                    <hr>
                    <p class="text-center text-muted mb-0">
                        Já tem uma conta? <a href="{{ route('login') }}" class="fw-bold" style="color: var(--pm-primary);">Faça login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Máscara simples para CPF
    document.getElementById('cpf').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0, 11);
        if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        e.target.value = v;
    });

    // Máscara simples para CEP
    document.getElementById('cep').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 8) v = v.slice(0, 8);
        if (v.length > 5) v = v.replace(/(\d{5})(\d{1,3})/, '$1-$2');
        e.target.value = v;
    });

    // Verificação em tempo real do status da promoção
    let promocaoAtiva = true;
    function verificarStatusPromocao() {
        fetch('{{ route("api.promocao.status") }}')
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
                    const form = document.getElementById('formRegistro');
                    if (form) {
                        form.querySelectorAll('input, select, button[type="submit"]').forEach(el => {
                            el.disabled = true;
                        });
                    }
                }
            })
            .catch(() => {});
    }
    setInterval(verificarStatusPromocao, 30000);

    const formRegistro = document.getElementById('formRegistro');
    if (formRegistro) {
        formRegistro.addEventListener('submit', function(e) {
            if (!promocaoAtiva) {
                e.preventDefault();
                alert('A promoção foi encerrada. Não é possível realizar novos cadastros.');
                return false;
            }
        });
    }
</script>
@endpush

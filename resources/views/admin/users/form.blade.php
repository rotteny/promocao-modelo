@extends('layouts.admin')

@section('title', ($admin ? 'Editar' : 'Novo') . ' Administrador - Admin')
@section('page-title', ($admin ? 'Editar' : 'Novo') . ' Administrador')

@push('styles')
<style>
    .perm-card {
        border: 2px solid #dee2e6;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        height: 100%;
    }
    .perm-card:hover {
        border-color: var(--pm-primary);
        background: rgba(111, 66, 193, 0.03);
    }
    .perm-card.active {
        border-color: var(--pm-primary);
        background: rgba(111, 66, 193, 0.08);
        box-shadow: 0 0 0 1px var(--pm-primary);
    }
    .perm-card .perm-icon {
        font-size: 1.5rem;
        color: var(--pm-primary);
    }
    .perm-card .form-check-input:checked {
        background-color: var(--pm-primary);
        border-color: var(--pm-primary);
    }

    [data-bs-theme="dark"] .perm-card {
        border-color: #2a2a40;
        background: var(--card-bg);
    }
    [data-bs-theme="dark"] .perm-card:hover {
        border-color: var(--pm-primary);
        background: rgba(111, 66, 193, 0.1);
    }
    [data-bs-theme="dark"] .perm-card.active {
        border-color: var(--pm-primary);
        background: rgba(111, 66, 193, 0.15);
    }

    .super-admin-toggle {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 16px 24px;
    }
    .super-admin-toggle .form-check-input {
        background-color: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
    }
    .super-admin-toggle .form-check-input:checked {
        background-color: #fff;
        border-color: #fff;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23dc3545'/%3e%3c/svg%3e");
    }
    .super-admin-toggle .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(255,255,255,0.3);
        border-color: #fff;
    }
</style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-{{ $admin ? 'pencil-square' : 'person-plus' }} me-2"></i>
                        {{ $admin ? 'Editar Administrador' : 'Novo Administrador' }}
                    </h2>
                    <p class="text-muted mb-0">
                        {{ $admin ? 'Altere os dados e permissões do administrador' : 'Preencha os dados para criar um novo administrador' }}
                    </p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>

            <form method="POST" action="{{ $admin ? route('admin.users.update', $admin) : route('admin.users.store') }}" id="adminForm">
                @csrf
                @if($admin)
                    @method('PUT')
                @endif

                {{-- Dados Pessoais --}}
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i>Dados de Acesso</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Nome Completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $admin?->name) }}"
                                       required maxlength="255" placeholder="Nome do administrador">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">E-mail</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $admin?->email) }}"
                                       required maxlength="255" placeholder="email@exemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">
                                    Senha
                                    @if($admin)
                                        <small class="text-muted fw-normal">(deixe em branco para manter)</small>
                                    @endif
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" minlength="6"
                                       {{ ! $admin ? 'required' : '' }}
                                       placeholder="{{ $admin ? '••••••' : 'Mínimo 6 caracteres' }}">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirmar Senha</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation"
                                       placeholder="Repita a senha">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-toggle-on me-2 text-success"></i>Status</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                                   {{ old('ativo', $admin?->ativo ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="ativo">
                                Conta ativa
                            </label>
                            <div class="form-text">Desmarque para impedir o login deste administrador.</div>
                        </div>
                    </div>
                </div>

                {{-- Super Admin --}}
                @if(Auth::guard('admin')->user()->is_super_admin)
                <div class="card mb-4">
                    <div class="card-body super-admin-toggle">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1"
                                   {{ old('is_super_admin', $admin?->is_super_admin) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_super_admin">
                                <i class="bi bi-shield-fill-check me-1"></i>Super Administrador
                            </label>
                            <div class="mt-1 small opacity-75">
                                Super administradores têm acesso total ao sistema e podem gerenciar outros administradores.
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Permissões --}}
                <div class="card mb-4" id="permissoesCard">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-key me-2 text-warning"></i>Permissões</h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">
                            Selecione quais funcionalidades este administrador poderá acessar.
                        </p>
                        <div class="row g-3">
                            @foreach($permissoes as $campo => $info)
                                <div class="col-md-6">
                                    <label class="perm-card d-block {{ old($campo, $admin?->{$campo}) ? 'active' : '' }}" id="card_{{ $campo }}">
                                        <div class="d-flex align-items-start">
                                            <div class="form-check">
                                                <input class="form-check-input perm-check" type="checkbox"
                                                       name="{{ $campo }}" value="1" id="{{ $campo }}"
                                                       {{ old($campo, $admin?->{$campo}) ? 'checked' : '' }}>
                                            </div>
                                            <div class="ms-2">
                                                <div class="fw-semibold">
                                                    <i class="bi {{ $info['icone'] }} perm-icon me-1"></i>
                                                    {{ $info['label'] }}
                                                </div>
                                                <div class="text-muted small mt-1">{{ $info['descricao'] }}</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Botões --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-pm btn-lg flex-grow-1">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ $admin ? 'Salvar Alterações' : 'Criar Administrador' }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-lg">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle visual dos cards de permissão
    document.querySelectorAll('.perm-check').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.perm-card');
            card.classList.toggle('active', this.checked);
        });
    });

    // Super admin desativa/habilita permissões individuais
    const superAdminCheck = document.getElementById('is_super_admin');
    const permissoesCard = document.getElementById('permissoesCard');

    function togglePermissoes() {
        if (!superAdminCheck || !permissoesCard) return;

        if (superAdminCheck.checked) {
            permissoesCard.style.opacity = '0.5';
            permissoesCard.style.pointerEvents = 'none';
            permissoesCard.querySelector('.card-header h6').innerHTML =
                '<i class="bi bi-key me-2 text-warning"></i>Permissões <span class="badge bg-secondary ms-2">Super Admin tem acesso total</span>';
        } else {
            permissoesCard.style.opacity = '1';
            permissoesCard.style.pointerEvents = 'auto';
            permissoesCard.querySelector('.card-header h6').innerHTML =
                '<i class="bi bi-key me-2 text-warning"></i>Permissões';
        }
    }

    if (superAdminCheck) {
        superAdminCheck.addEventListener('change', togglePermissoes);
        togglePermissoes();
    }
});
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Administradores - Admin')
@section('page-title', 'Administradores')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-person-gear me-2"></i>Administradores</h2>
            <p class="text-muted mb-0">Gerencie os usuários administrativos e suas permissões</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-pm">
            <i class="bi bi-person-plus me-1"></i>Novo Administrador
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th class="text-center">Permissões</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 140px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr class="{{ ! $admin->ativo ? 'opacity-50' : '' }}">
                            <td>
                                <div class="fw-semibold">{{ $admin->name }}</div>
                                @if($admin->id === Auth::guard('admin')->id())
                                    <small class="text-primary"><i class="bi bi-person-check me-1"></i>Você</small>
                                @endif
                            </td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                @if($admin->is_super_admin)
                                    <span class="badge bg-danger"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>
                                @else
                                    <span class="badge bg-secondary">Administrador</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($admin->is_super_admin)
                                    <span class="text-muted small">Todas</span>
                                @else
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        @if($admin->perm_produtos)
                                            <span class="badge bg-info bg-opacity-75" title="Produtos"><i class="bi bi-box-seam"></i></span>
                                        @endif
                                        @if($admin->perm_faq)
                                            <span class="badge bg-warning bg-opacity-75" title="FAQ"><i class="bi bi-question-circle"></i></span>
                                        @endif
                                        @if($admin->perm_configuracoes)
                                            <span class="badge bg-primary bg-opacity-75" title="Configurações"><i class="bi bi-gear"></i></span>
                                        @endif
                                        @if($admin->perm_encerrar_campanha)
                                            <span class="badge bg-danger bg-opacity-75" title="Encerrar Campanha"><i class="bi bi-shield-lock"></i></span>
                                        @endif
                                        @if(! $admin->perm_produtos && ! $admin->perm_faq && ! $admin->perm_configuracoes && ! $admin->perm_encerrar_campanha)
                                            <span class="text-muted small">Nenhuma</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($admin->ativo)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary">Inativo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.users.edit', $admin) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($admin->id !== Auth::guard('admin')->id())
                                    <form action="{{ route('admin.users.destroy', $admin) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir o administrador &quot;{{ $admin->name }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                Nenhum administrador encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($admins->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $admins->links() }}
        </div>
    @endif
@endsection

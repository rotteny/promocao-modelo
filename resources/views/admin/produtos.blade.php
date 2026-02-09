@extends('layouts.admin')

@section('title', 'Produtos - Admin')
@section('page-title', 'Produtos')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Produtos Participantes</h2>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>

            <!-- Formulário de Cadastro -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>Cadastrar Novo Produto</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.produtos.store') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Descrição <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('descricao') is-invalid @enderror"
                                       name="descricao" value="{{ old('descricao') }}" required>
                                @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="bonus" value="1" id="bonus" {{ old('bonus') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="bonus">
                                        <i class="bi bi-star-fill text-warning me-1"></i>Bônus
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-pm w-100">
                                    <i class="bi bi-plus me-1"></i>Cadastrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Produtos -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Bônus</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produtos as $produto)
                                    <tr>
                                        <td>{{ $produto->id }}</td>
                                        <td class="fw-semibold">{{ $produto->descricao }}</td>
                                        <td>
                                            @if($produto->bonus)
                                                <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Sim</span>
                                            @else
                                                <span class="badge bg-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $produto->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <form action="{{ route('admin.produtos.destroy', $produto) }}" method="POST"
                                                  onsubmit="return confirm('Tem certeza que deseja remover este produto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Nenhum produto cadastrado.
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

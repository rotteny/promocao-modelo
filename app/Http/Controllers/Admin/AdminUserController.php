<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Lista todos os administradores.
     */
    public function index()
    {
        $admins = User::orderByDesc('is_super_admin')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('admins'));
    }

    /**
     * Formulário de criação de novo admin.
     */
    public function create()
    {
        $permissoes = User::permissoesDisponiveis();

        return view('admin.users.form', [
            'admin' => null,
            'permissoes' => $permissoes,
        ]);
    }

    /**
     * Salva novo admin.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'is_super_admin' => ['nullable', 'boolean'],
            'perm_produtos' => ['nullable', 'boolean'],
            'perm_faq' => ['nullable', 'boolean'],
            'perm_configuracoes' => ['nullable', 'boolean'],
            'perm_encerrar_campanha' => ['nullable', 'boolean'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        // Apenas super admin pode criar outro super admin
        $currentAdmin = Auth::guard('admin')->user();
        if (! $currentAdmin->is_super_admin) {
            $dados['is_super_admin'] = false;
        }

        $dados['is_super_admin'] = (bool) ($dados['is_super_admin'] ?? false);
        $dados['perm_produtos'] = (bool) ($dados['perm_produtos'] ?? false);
        $dados['perm_faq'] = (bool) ($dados['perm_faq'] ?? false);
        $dados['perm_configuracoes'] = (bool) ($dados['perm_configuracoes'] ?? false);
        $dados['perm_encerrar_campanha'] = (bool) ($dados['perm_encerrar_campanha'] ?? false);
        $dados['ativo'] = (bool) ($dados['ativo'] ?? true);

        User::create($dados);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Administrador criado com sucesso.');
    }

    /**
     * Formulário de edição de admin.
     */
    public function edit(User $user)
    {
        $permissoes = User::permissoesDisponiveis();

        return view('admin.users.form', [
            'admin' => $user,
            'permissoes' => $permissoes,
        ]);
    }

    /**
     * Atualiza admin existente.
     */
    public function update(Request $request, User $user)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_super_admin' => ['nullable', 'boolean'],
            'perm_produtos' => ['nullable', 'boolean'],
            'perm_faq' => ['nullable', 'boolean'],
            'perm_configuracoes' => ['nullable', 'boolean'],
            'perm_encerrar_campanha' => ['nullable', 'boolean'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $currentAdmin = Auth::guard('admin')->user();

        // Não pode desativar a si mesmo
        if ($user->id === $currentAdmin->id && isset($dados['ativo']) && ! $dados['ativo']) {
            return back()->with('error', 'Você não pode desativar sua própria conta.');
        }

        // Não pode remover super admin de si mesmo
        if ($user->id === $currentAdmin->id && $currentAdmin->is_super_admin) {
            $dados['is_super_admin'] = true;
        }

        // Apenas super admin pode alterar flag super_admin de outros
        if (! $currentAdmin->is_super_admin) {
            unset($dados['is_super_admin']);
        }

        // Converte checkboxes (não marcados não são enviados)
        $dados['is_super_admin'] = (bool) ($dados['is_super_admin'] ?? false);
        $dados['perm_produtos'] = (bool) ($dados['perm_produtos'] ?? false);
        $dados['perm_faq'] = (bool) ($dados['perm_faq'] ?? false);
        $dados['perm_configuracoes'] = (bool) ($dados['perm_configuracoes'] ?? false);
        $dados['perm_encerrar_campanha'] = (bool) ($dados['perm_encerrar_campanha'] ?? false);
        $dados['ativo'] = (bool) ($dados['ativo'] ?? false);

        // Se password vazio, remove para não sobrescrever
        if (empty($dados['password'])) {
            unset($dados['password']);
        }

        $user->update($dados);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Administrador \"{$user->name}\" atualizado com sucesso.");
    }

    /**
     * Remove admin (soft: desativa).
     */
    public function destroy(User $user)
    {
        $currentAdmin = Auth::guard('admin')->user();

        if ($user->id === $currentAdmin->id) {
            return back()->with('error', 'Você não pode excluir sua própria conta.');
        }

        // Não permitir excluir o último super admin
        if ($user->is_super_admin) {
            $outrosSuperAdmin = User::where('is_super_admin', true)
                ->where('id', '!=', $user->id)
                ->exists();

            if (! $outrosSuperAdmin) {
                return back()->with('error', 'Não é possível excluir o único Super Administrador do sistema.');
            }
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Administrador \"{$user->name}\" removido com sucesso.");
    }
}

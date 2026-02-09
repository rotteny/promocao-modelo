<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProdutoParticipante;

class AdminProdutoController extends Controller
{
    /**
     * Lista todos os produtos participantes.
     */
    public function index()
    {
        $produtos = ProdutoParticipante::orderBy('descricao')->get();

        return view('admin.produtos', compact('produtos'));
    }

    /**
     * Cadastra um novo produto participante.
     */
    public function store()
    {
        $dados = request()->validate([
            'descricao' => ['required', 'string', 'max:255'],
            'bonus' => ['nullable', 'boolean'],
        ]);

        ProdutoParticipante::create([
            'descricao' => $dados['descricao'],
            'bonus' => $dados['bonus'] ?? false,
        ]);

        return back()->with('success', 'Produto cadastrado com sucesso.');
    }

    /**
     * Remove um produto participante.
     */
    public function destroy(ProdutoParticipante $produto)
    {
        $produto->delete();

        return back()->with('success', 'Produto removido com sucesso.');
    }
}

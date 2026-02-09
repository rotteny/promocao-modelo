<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class AdminFaqController extends Controller
{
    /**
     * Lista todas as FAQs para gestão.
     */
    public function index()
    {
        $faqs = Faq::orderBy('ordem')->orderBy('id')->get();

        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create()
    {
        return view('admin.faqs.form', [
            'faq' => null,
        ]);
    }

    /**
     * Salva uma nova FAQ.
     */
    public function store()
    {
        $dados = request()->validate([
            'pergunta' => ['required', 'string', 'max:500'],
            'resposta' => ['required', 'string'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        Faq::create([
            'pergunta' => $dados['pergunta'],
            'resposta' => $dados['resposta'],
            'ordem' => $dados['ordem'] ?? 0,
            'ativo' => $dados['ativo'] ?? true,
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Pergunta cadastrada com sucesso.');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Faq $faq)
    {
        return view('admin.faqs.form', compact('faq'));
    }

    /**
     * Atualiza uma FAQ existente.
     */
    public function update(Faq $faq)
    {
        $dados = request()->validate([
            'pergunta' => ['required', 'string', 'max:500'],
            'resposta' => ['required', 'string'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $faq->update([
            'pergunta' => $dados['pergunta'],
            'resposta' => $dados['resposta'],
            'ordem' => $dados['ordem'] ?? 0,
            'ativo' => request()->has('ativo'),
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Pergunta atualizada com sucesso.');
    }

    /**
     * Remove uma FAQ.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', 'Pergunta removida com sucesso.');
    }
}

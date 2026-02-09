<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Participante;
use App\Services\PromocaoService;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(
        private readonly PromocaoService $promocaoService,
    ) {}

    public function showRegistrationForm()
    {
        $promocao = $this->promocaoService;

        return view('auth.register', compact('promocao'));
    }

    public function register(RegisterRequest $request)
    {
        // Verificação final antes de salvar (proteção contra submissão após encerramento)
        if (! $this->promocaoService->isAtiva()) {
            return redirect()->route('home')
                ->with('warning', $this->promocaoService->getMensagemStatus());
        }

        $participante = Participante::create([
            'name' => $request->name,
            'cpf' => $request->cpf,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'celular' => $request->celular,
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'cep' => $request->cep,
            'password' => $request->password,
        ]);

        Auth::guard('web')->login($participante);

        return redirect()->route('dashboard')
            ->with('success', 'Cadastro realizado com sucesso! Bem-vindo à Promoção Modelo.');
    }
}

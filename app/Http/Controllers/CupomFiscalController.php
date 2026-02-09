<?php

namespace App\Http\Controllers;

use App\Contracts\InvoiceValidatorInterface;
use App\Http\Requests\StoreCupomFiscalRequest;
use App\Models\ProdutoParticipante;
use App\Services\CupomFiscalService;
use App\Services\PromocaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CupomFiscalController extends Controller
{
    public function __construct(
        private readonly CupomFiscalService $cupomFiscalService,
        private readonly PromocaoService $promocaoService,
        private readonly InvoiceValidatorInterface $invoiceValidator,
    ) {}

    /**
     * Exibe o formulário de cadastro de cupom fiscal.
     */
    public function create()
    {
        $produtos = ProdutoParticipante::orderBy('descricao')->get();
        $promocao = $this->promocaoService;

        return view('cupom.create', compact('produtos', 'promocao'));
    }

    /**
     * Processa o cadastro do cupom fiscal.
     */
    public function store(StoreCupomFiscalRequest $request)
    {
        // Verificação final antes de processar (proteção contra submissão após encerramento)
        if (! $this->promocaoService->isAtiva()) {
            return redirect()->route('dashboard')
                ->with('warning', $this->promocaoService->getMensagemStatus());
        }

        $cupom = $this->cupomFiscalService->criarCupom(
            Auth::guard('web')->id(),
            $request->validated()
        );

        if ($cupom->status === 'rejeitado') {
            return redirect()->route('dashboard')
                ->with('warning', 'O cupom foi cadastrado, mas não passou na validação. Verifique os dados e tente novamente.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Cupom cadastrado com sucesso! Seus números da sorte serão gerados em instantes.');
    }

    /**
     * Consulta os dados de uma nota fiscal a partir dos dados do QR Code.
     * Endpoint AJAX consumido pelo scanner de QR Code na view de cadastro.
     */
    public function consultarQrCode(Request $request): JsonResponse
    {
        $request->validate([
            'qrcode_data' => ['required', 'string', 'min:10'],
        ]);

        $resultado = $this->invoiceValidator->consultarNota($request->input('qrcode_data'));

        if (! $resultado->success) {
            return response()->json([
                'success' => false,
                'message' => $resultado->message,
            ], 200); // 200 para que o JS trate como resposta válida
        }

        // Verifica se há produtos participantes nos itens da nota
        if (empty($resultado->produtosParticipantes)) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum produto participante da promoção foi encontrado neste cupom fiscal. Você pode continuar com o cadastro manual.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $resultado->message,
            'data' => [
                'chave_acesso' => $resultado->chaveAcesso,
                'numero' => $resultado->numero,
                'cnpj_loja' => $resultado->cnpjLoja,
                'data_compra' => $resultado->dataCompra,
                'valor_total' => $resultado->valorTotal,
                'itens' => $resultado->itens,
                'produtos_participantes' => $resultado->produtosParticipantes,
            ],
        ]);
    }
}

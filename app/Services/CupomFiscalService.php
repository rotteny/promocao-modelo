<?php

namespace App\Services;

use App\Contracts\InvoiceValidatorInterface;
use App\Jobs\ProcessarCupomFiscal;
use App\Models\CupomFiscal;
use App\Models\ItemCupom;
use Illuminate\Support\Facades\DB;

class CupomFiscalService
{
    public function __construct(
        private readonly InvoiceValidatorInterface $invoiceValidator,
    ) {}

    /**
     * Cria um cupom fiscal com seus itens.
     * Se validado, despacha o processamento assíncrono dos números da sorte.
     */
    public function criarCupom(int $participanteId, array $dados): CupomFiscal
    {
        return DB::transaction(function () use ($participanteId, $dados) {
            // Calcula valor total a partir dos itens
            $valorTotal = collect($dados['itens'])->sum(function ($item) {
                return $item['quantidade'] * $item['valor_unitario'];
            });

            // Cria o cupom
            $cupom = CupomFiscal::create([
                'numero' => $dados['numero'],
                'cnpj_loja' => $dados['cnpj_loja'],
                'chave_acesso' => $dados['chave_acesso'] ?? null,
                'data_compra' => $dados['data_compra'],
                'valor_total' => $valorTotal,
                'status' => CupomFiscal::STATUS_PENDENTE,
                'participante_id' => $participanteId,
            ]);

            // Cria os itens do cupom
            foreach ($dados['itens'] as $itemData) {
                ItemCupom::create([
                    'cupom_fiscal_id' => $cupom->id,
                    'produto_participante_id' => $itemData['produto_participante_id'],
                    'quantidade' => $itemData['quantidade'],
                    'valor_unitario' => $itemData['valor_unitario'],
                    'subtotal' => $itemData['quantidade'] * $itemData['valor_unitario'],
                ]);
            }

            // Valida via Sefaz (Mock)
            if ($cupom->chave_acesso) {
                $resultado = $this->invoiceValidator->validate($cupom->chave_acesso);
                $cupom->update([
                    'status' => $resultado->isValid
                        ? CupomFiscal::STATUS_VALIDADO
                        : CupomFiscal::STATUS_REJEITADO,
                ]);
            } else {
                $cupom->update(['status' => CupomFiscal::STATUS_VALIDADO]);
            }

            // Recarrega relacionamentos
            $cupom->refresh();
            $cupom->load('itens.produto');

            // Se validado, despacha para processamento assíncrono na fila
            if ($cupom->status === CupomFiscal::STATUS_VALIDADO) {
                ProcessarCupomFiscal::dispatch($cupom);
            }

            return $cupom;
        });
    }
}

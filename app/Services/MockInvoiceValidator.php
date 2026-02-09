<?php

namespace App\Services;

use App\Contracts\InvoiceQueryResult;
use App\Contracts\InvoiceValidationResult;
use App\Contracts\InvoiceValidatorInterface;
use App\Models\ProdutoParticipante;

/**
 * Mock de validação e consulta de cupom fiscal.
 * Simula a consulta à Sefaz para desenvolvimento e testes.
 *
 * Na implementação real, o método consultarNota() faria um HTTP request
 * para a API da Sefaz do estado correspondente, parseando o XML/HTML
 * retornado para extrair os dados da NFC-e.
 */
class MockInvoiceValidator implements InvoiceValidatorInterface
{
    public function validate(string $chaveAcesso): InvoiceValidationResult
    {
        // Validação básica do formato da chave
        if (strlen($chaveAcesso) !== 44 || ! ctype_digit($chaveAcesso)) {
            return InvoiceValidationResult::invalid(
                'Chave de acesso inválida. Deve conter 44 dígitos numéricos.'
            );
        }

        // Simula validação com sucesso (Mock)
        return InvoiceValidationResult::valid(
            valorTotal: rand(2000, 50000) / 100,
            numero: substr($chaveAcesso, 25, 9),
            itens: [],
        );
    }

    /**
     * Simula a consulta dos dados de uma NFC-e a partir da URL do QR Code.
     *
     * Em produção, este método deveria:
     * 1. Fazer HTTP GET na URL do QR Code
     * 2. Parsear o HTML/XML retornado pela Sefaz
     * 3. Extrair: chave de acesso, número, data, valor total e itens
     * 4. Cruzar os itens da nota com os produtos participantes cadastrados
     */
    public function consultarNota(string $qrCodeUrl): InvoiceQueryResult
    {
        // Tenta extrair a chave de acesso da URL do QR Code
        $chaveAcesso = $this->extrairChaveAcesso($qrCodeUrl);

        if (! $chaveAcesso) {
            return InvoiceQueryResult::falha(
                'Não foi possível identificar a chave de acesso no QR Code. O formato da URL não é reconhecido.'
            );
        }

        // Validação básica da chave
        if (strlen($chaveAcesso) !== 44 || ! ctype_digit($chaveAcesso)) {
            return InvoiceQueryResult::falha(
                'A chave de acesso extraída do QR Code é inválida.'
            );
        }

        // Simula dados da nota fiscal
        // Na chave NFe de 44 dígitos, posições 6-19 contêm o CNPJ do emitente
        $numero = substr($chaveAcesso, 25, 9);
        $cnpjLoja = substr($chaveAcesso, 6, 14);
        $dataCompra = now()->format('Y-m-d');
        $valorTotal = 0;

        // Busca produtos participantes cadastrados para simular o cruzamento
        $produtosParticipantes = ProdutoParticipante::all();

        if ($produtosParticipantes->isEmpty()) {
            return InvoiceQueryResult::falha(
                'Nenhum produto participante cadastrado na promoção. Não é possível cruzar os dados da nota.'
            );
        }

        // Simula itens da nota: seleciona aleatoriamente 1-3 produtos participantes
        $produtosSelecionados = $produtosParticipantes->random(min(rand(1, 3), $produtosParticipantes->count()));
        $itensNota = [];
        $produtosEncontrados = [];

        foreach ($produtosSelecionados as $produto) {
            $quantidade = rand(1, 5);
            $valorUnitario = round(rand(500, 5000) / 100, 2);
            $subtotal = round($quantidade * $valorUnitario, 2);
            $valorTotal += $subtotal;

            $itensNota[] = [
                'descricao_nota' => $produto->descricao, // Na Sefaz real, seria a descrição do item na nota
                'quantidade' => $quantidade,
                'valor_unitario' => $valorUnitario,
                'subtotal' => $subtotal,
            ];

            $produtosEncontrados[] = [
                'produto_participante_id' => $produto->id,
                'descricao' => $produto->descricao,
                'bonus' => $produto->bonus,
                'quantidade' => $quantidade,
                'valor_unitario' => $valorUnitario,
                'subtotal' => $subtotal,
            ];
        }

        $valorTotal = round($valorTotal, 2);

        return InvoiceQueryResult::sucesso(
            chaveAcesso: $chaveAcesso,
            numero: ltrim($numero, '0') ?: '0',
            cnpjLoja: $cnpjLoja,
            dataCompra: $dataCompra,
            valorTotal: $valorTotal,
            itens: $itensNota,
            produtosParticipantes: $produtosEncontrados,
        );
    }

    /**
     * Extrai a chave de acesso (chNFe) de diferentes formatos de URL de QR Code NFC-e.
     *
     * Formatos suportados:
     * - URL com parâmetro chNFe: ...?chNFe=12345678901234567890123456789012345678901234
     * - URL com parâmetro p: ...?p=12345678901234567890123456789012345678901234|...
     * - String pura de 44 dígitos (chave colada diretamente)
     */
    private function extrairChaveAcesso(string $qrCodeUrl): ?string
    {
        // Se é uma string pura de 44 dígitos
        $limpo = trim($qrCodeUrl);
        if (strlen($limpo) === 44 && ctype_digit($limpo)) {
            return $limpo;
        }

        // Tenta extrair de URL com parâmetro chNFe
        if (preg_match('/[?&]chNFe=(\d{44})/', $qrCodeUrl, $matches)) {
            return $matches[1];
        }

        // Tenta extrair de URL com parâmetro p (formato pipe-separated)
        if (preg_match('/[?&]p=(\d{44})\|/', $qrCodeUrl, $matches)) {
            return $matches[1];
        }

        // Tenta encontrar qualquer sequência de 44 dígitos na string
        if (preg_match('/(\d{44})/', $qrCodeUrl, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

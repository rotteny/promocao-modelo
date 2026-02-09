<?php

namespace App\Contracts;

/**
 * Interface para validação e consulta de cupons fiscais junto à Sefaz.
 * Permite fácil substituição do Mock pela implementação real futura.
 */
interface InvoiceValidatorInterface
{
    /**
     * Valida um cupom fiscal pela chave de acesso.
     *
     * @param string $chaveAcesso Chave de acesso de 44 dígitos
     * @return InvoiceValidationResult
     */
    public function validate(string $chaveAcesso): InvoiceValidationResult;

    /**
     * Consulta os dados completos de uma nota fiscal a partir da URL do QR Code.
     * Retorna os dados da nota incluindo número, data, valor e itens.
     *
     * @param string $qrCodeUrl URL extraída do QR Code da NFC-e
     * @return InvoiceQueryResult
     */
    public function consultarNota(string $qrCodeUrl): InvoiceQueryResult;
}

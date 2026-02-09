<?php

namespace App\Contracts;

/**
 * Resultado da consulta de uma nota fiscal na Sefaz.
 */
class InvoiceQueryResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?string $chaveAcesso = null,
        public readonly ?string $numero = null,
        public readonly ?string $cnpjLoja = null,
        public readonly ?string $dataCompra = null,
        public readonly ?float $valorTotal = null,
        public readonly array $itens = [],
        public readonly array $produtosParticipantes = [],
    ) {}

    /**
     * Consulta bem-sucedida com dados da nota.
     */
    public static function sucesso(
        string $chaveAcesso,
        string $numero,
        string $cnpjLoja,
        string $dataCompra,
        float $valorTotal,
        array $itens = [],
        array $produtosParticipantes = [],
    ): self {
        return new self(
            success: true,
            message: 'Nota fiscal consultada com sucesso.',
            chaveAcesso: $chaveAcesso,
            numero: $numero,
            cnpjLoja: $cnpjLoja,
            dataCompra: $dataCompra,
            valorTotal: $valorTotal,
            itens: $itens,
            produtosParticipantes: $produtosParticipantes,
        );
    }

    /**
     * Falha na consulta.
     */
    public static function falha(string $message): self
    {
        return new self(
            success: false,
            message: $message,
        );
    }
}

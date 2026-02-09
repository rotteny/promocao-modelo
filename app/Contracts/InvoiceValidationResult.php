<?php

namespace App\Contracts;

/**
 * Resultado da validação de um cupom fiscal.
 */
class InvoiceValidationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly string $message,
        public readonly ?float $valorTotal = null,
        public readonly ?string $numero = null,
        public readonly array $itens = [],
    ) {}

    public static function valid(float $valorTotal, string $numero, array $itens = []): self
    {
        return new self(
            isValid: true,
            message: 'Cupom fiscal validado com sucesso.',
            valorTotal: $valorTotal,
            numero: $numero,
            itens: $itens,
        );
    }

    public static function invalid(string $message): self
    {
        return new self(
            isValid: false,
            message: $message,
        );
    }
}

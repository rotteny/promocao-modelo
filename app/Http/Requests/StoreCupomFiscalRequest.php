<?php

namespace App\Http\Requests;

use App\Services\PromocaoService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCupomFiscalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $promocao = app(PromocaoService::class);
        $dataInicio = $promocao->getDataInicio()->toDateString();
        $dataFim = min($promocao->getDataFim()->toDateString(), now()->toDateString());

        return [
            'numero' => [
                'required',
                'string',
                'regex:/^\d+$/',
                'max:255',
                Rule::unique('cupons_fiscais')->where(function ($query) {
                    return $query->where('cnpj_loja', $this->input('cnpj_loja'));
                }),
            ],
            'cnpj_loja' => ['required', 'string', 'regex:/^\d{14}$/'],
            'chave_acesso' => ['nullable', 'string', 'max:44'],
            'data_compra' => [
                'required',
                'date',
                "after_or_equal:{$dataInicio}",
                "before_or_equal:{$dataFim}",
            ],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_participante_id' => ['required', 'exists:produtos_participantes,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
            'itens.*.valor_unitario' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * Validação adicional: valor mínimo de R$ 20,00 em produtos.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $itens = $this->input('itens', []);
            $valorTotal = 0;

            foreach ($itens as $item) {
                $quantidade = (int) ($item['quantidade'] ?? 0);
                $valorUnitario = (float) ($item['valor_unitario'] ?? 0);
                $valorTotal += $quantidade * $valorUnitario;
            }

            if ($valorTotal < 20) {
                $validator->errors()->add(
                    'itens',
                    'O valor total dos produtos deve ser de no mínimo R$ 20,00. Valor atual: R$ ' . number_format($valorTotal, 2, ',', '.')
                );
            }
        });
    }

    public function messages(): array
    {
        $promocao = app(PromocaoService::class);

        return [
            'numero.required' => 'O número do cupom é obrigatório.',
            'numero.regex' => 'O número do cupom deve conter apenas dígitos numéricos.',
            'numero.unique' => 'Este cupom já foi cadastrado para esta loja.',
            'cnpj_loja.required' => 'O CNPJ da loja é obrigatório.',
            'cnpj_loja.regex' => 'O CNPJ da loja deve conter exatamente 14 dígitos numéricos.',
            'data_compra.required' => 'A data da compra é obrigatória.',
            'data_compra.date' => 'Informe uma data válida.',
            'data_compra.after_or_equal' => 'A data da compra deve ser a partir de ' . $promocao->getDataInicio()->format('d/m/Y') . ' (início da promoção).',
            'data_compra.before_or_equal' => 'A data da compra não pode ser futura.',
            'itens.required' => 'É necessário adicionar pelo menos um item ao cupom.',
            'itens.min' => 'É necessário adicionar pelo menos um item ao cupom.',
            'itens.*.produto_participante_id.required' => 'Selecione um produto para cada item.',
            'itens.*.produto_participante_id.exists' => 'Produto selecionado não encontrado.',
            'itens.*.quantidade.required' => 'Informe a quantidade de cada item.',
            'itens.*.quantidade.min' => 'A quantidade deve ser no mínimo 1.',
            'itens.*.valor_unitario.required' => 'Informe o valor unitário de cada item.',
            'itens.*.valor_unitario.min' => 'O valor unitário deve ser no mínimo R$ 0,01.',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\CupomFiscal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CuponsFiscaisExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    private ?string $busca;
    private ?string $status;

    public function __construct(?string $busca = null, ?string $status = null)
    {
        $this->busca = $busca;
        $this->status = $status;
    }

    public function query()
    {
        $query = CupomFiscal::query()
            ->with('participante')
            ->withCount('numerosDaSorte');

        if ($this->busca) {
            $query->where(function ($q) {
                $q->where('numero', 'ilike', "%{$this->busca}%")
                  ->orWhereHas('participante', function ($q2) {
                      $q2->where('name', 'ilike', "%{$this->busca}%")
                         ->orWhere('cpf', 'ilike', "%{$this->busca}%");
                  });
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Número do Cupom',
            'CNPJ Loja',
            'Participante',
            'CPF',
            'Data da Compra',
            'Valor Total (R$)',
            'Status',
            'Nº da Sorte Gerados',
            'Erro',
            'Data de Cadastro',
        ];
    }

    public function map($cupom): array
    {
        $cnpj = $cupom->cnpj_loja;
        $cnpjFormatado = $cnpj
            ? substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2)
            : '-';

        return [
            $cupom->id,
            $cupom->numero,
            $cnpjFormatado,
            $cupom->participante?->name ?? 'N/A',
            $cupom->participante?->cpf ?? 'N/A',
            $cupom->data_compra?->format('d/m/Y') ?? '-',
            number_format((float) $cupom->valor_total, 2, ',', '.'),
            $cupom->status_label,
            $cupom->numeros_da_sorte_count,
            $cupom->erro_processamento ?? '-',
            $cupom->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

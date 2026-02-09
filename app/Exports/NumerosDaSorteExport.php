<?php

namespace App\Exports;

use App\Models\NumeroDaSorte;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NumerosDaSorteExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    private ?string $busca;
    private ?int $serie;

    public function __construct(?string $busca = null, ?int $serie = null)
    {
        $this->busca = $busca;
        $this->serie = $serie;
    }

    public function query()
    {
        $query = NumeroDaSorte::query()
            ->with(['participante', 'cupomFiscal']);

        if ($this->busca) {
            $query->where(function ($q) {
                $q->whereHas('participante', function ($q2) {
                    $q2->where('name', 'ilike', "%{$this->busca}%")
                       ->orWhere('cpf', 'ilike', "%{$this->busca}%");
                });
            });
        }

        if ($this->serie !== null) {
            $query->where('serie', $this->serie);
        }

        return $query->orderBy('serie')->orderBy('numero');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Série',
            'Número',
            'Número Formatado',
            'Participante',
            'CPF',
            'Cupom Fiscal',
            'Data de Geração',
        ];
    }

    public function map($numero): array
    {
        return [
            $numero->id,
            $numero->serie,
            str_pad($numero->numero, 4, '0', STR_PAD_LEFT),
            $numero->numero_formatado,
            $numero->participante?->name ?? 'N/A',
            $numero->participante?->cpf ?? 'N/A',
            $numero->cupomFiscal?->numero ?? 'N/A',
            $numero->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

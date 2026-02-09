<?php

namespace App\Exports;

use App\Models\Participante;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParticipantesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    private ?string $busca;

    public function __construct(?string $busca = null)
    {
        $this->busca = $busca;
    }

    public function query()
    {
        $query = Participante::query()
            ->withCount(['cuponsFiscais', 'numerosDaSorte']);

        if ($this->busca) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', "%{$this->busca}%")
                  ->orWhere('cpf', 'ilike', "%{$this->busca}%")
                  ->orWhere('email', 'ilike', "%{$this->busca}%")
                  ->orWhere('cidade', 'ilike', "%{$this->busca}%");
            });
        }

        return $query->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'CPF',
            'E-mail',
            'Telefone',
            'Celular',
            'Cidade',
            'Estado',
            'CEP',
            'Total de Cupons',
            'Total Números da Sorte',
            'Data de Cadastro',
        ];
    }

    public function map($participante): array
    {
        return [
            $participante->id,
            $participante->name,
            $participante->cpf,
            $participante->email,
            $participante->telefone,
            $participante->celular,
            $participante->cidade,
            $participante->estado,
            $participante->cep,
            $participante->cupons_fiscais_count,
            $participante->numeros_da_sorte_count,
            $participante->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

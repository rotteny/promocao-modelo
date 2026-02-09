<?php

namespace App\Services;

use App\Models\CupomFiscal;
use App\Models\NumeroDaSorte;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LuckyNumberService
{
    /**
     * Constantes de configuração das séries.
     */
    public const int TOTAL_SERIES = 10;         // Séries de 0 a 9
    public const int NUMEROS_POR_SERIE = 10000;  // Números de 0000 a 9999

    /**
     * Retorna o valor em reais necessário para ganhar 1 número da sorte.
     */
    public function getValorPorNumero(): float
    {
        return (float) Setting::getValue('valor_por_numero', 20.00);
    }

    /**
     * Calcula quantos números da sorte um cupom deve receber.
     *
     * Regra: 1 número a cada R$ 20 em produtos participantes.
     * Se houver produtos bônus, o participante ganha +1 número
     * a cada R$ 20 do valor dos produtos bônus (contagem dobrada).
     */
    public function calcularQuantidadeNumeros(CupomFiscal $cupom): int
    {
        $valorPorNumero = $this->getValorPorNumero();

        // Números base: valor total do cupom / R$ 20
        $numerosBase = (int) floor((float) $cupom->valor_total / $valorPorNumero);

        // Números bônus: valor dos produtos bônus / R$ 20
        $valorBonus = $cupom->getValorProdutosBonus();
        $numerosBonus = (int) floor($valorBonus / $valorPorNumero);

        return $numerosBase + $numerosBonus;
    }

    /**
     * Gera números da sorte para um cupom fiscal.
     *
     * A distribuição segue round-robin pelas séries:
     * cada número gerado vai para a série seguinte à última distribuída,
     * começando em 0 e ciclando até a última, reiniciando ao final.
     *
     * @return Collection<int, NumeroDaSorte>
     */
    public function gerarNumeros(CupomFiscal $cupom): Collection
    {
        $quantidade = $this->calcularQuantidadeNumeros($cupom);

        if ($quantidade <= 0) {
            return collect();
        }

        return DB::transaction(function () use ($cupom, $quantidade) {
            $numerosGerados = collect();

            // Descobre a próxima série a ser usada (a série seguinte à última distribuída)
            $proximaSerie = $this->getProximaSerie();

            for ($i = 0; $i < $quantidade; $i++) {
                $numeroDaSorte = $this->gerarNumeroNaSerie(
                    $proximaSerie,
                    $cupom->participante_id,
                    $cupom->id
                );

                if ($numeroDaSorte === null) {
                    // A série atual está cheia, tenta encontrar a próxima disponível
                    $numeroDaSorte = $this->gerarNumeroEmSerieDisponivel(
                        $proximaSerie,
                        $cupom->participante_id,
                        $cupom->id
                    );

                    if ($numeroDaSorte === null) {
                        break; // Todas as séries estão cheias
                    }

                    // Atualiza para a série onde efetivamente foi criado
                    $proximaSerie = $numeroDaSorte->serie;
                }

                $numerosGerados->push($numeroDaSorte);

                // Avança para a próxima série (round-robin)
                $proximaSerie = ($proximaSerie + 1) % self::TOTAL_SERIES;
            }

            return $numerosGerados;
        });
    }

    /**
     * Descobre qual é a próxima série a ser usada.
     * É a série seguinte à última que recebeu um número.
     * Se nenhum número foi distribuído, começa na série 0.
     */
    public function getProximaSerie(): int
    {
        $ultimo = NumeroDaSorte::orderByDesc('id')->first();

        if (! $ultimo) {
            return 0;
        }

        return ($ultimo->serie + 1) % self::TOTAL_SERIES;
    }

    /**
     * Gera um número aleatório em uma série específica.
     * Retorna null se a série estiver cheia.
     */
    private function gerarNumeroNaSerie(int $serie, int $participanteId, int $cupomId): ?NumeroDaSorte
    {
        $usados = NumeroDaSorte::where('serie', $serie)
            ->pluck('numero')
            ->toArray();

        // Se a série está cheia, retorna null
        if (count($usados) >= self::NUMEROS_POR_SERIE) {
            return null;
        }

        // Gera um número aleatório que não está em uso nesta série
        $numero = $this->gerarNumeroAleatorioNaoUsado($usados);

        return NumeroDaSorte::create([
            'numero' => $numero,
            'serie' => $serie,
            'participante_id' => $participanteId,
            'cupom_fiscal_id' => $cupomId,
        ]);
    }

    /**
     * Tenta gerar um número em qualquer série disponível, começando
     * a busca a partir da série informada e seguindo em round-robin.
     */
    private function gerarNumeroEmSerieDisponivel(int $serieInicial, int $participanteId, int $cupomId): ?NumeroDaSorte
    {
        for ($tentativa = 0; $tentativa < self::TOTAL_SERIES; $tentativa++) {
            $serie = ($serieInicial + $tentativa) % self::TOTAL_SERIES;
            $resultado = $this->gerarNumeroNaSerie($serie, $participanteId, $cupomId);

            if ($resultado !== null) {
                return $resultado;
            }
        }

        // Todas as séries estão cheias
        return null;
    }

    /**
     * Gera um número aleatório entre 0 e 9999 que não esteja na lista de usados.
     */
    private function gerarNumeroAleatorioNaoUsado(array $usados): int
    {
        $todosNumeros = range(0, self::NUMEROS_POR_SERIE - 1);
        $disponiveis = array_diff($todosNumeros, $usados);

        $chave = array_rand($disponiveis);

        return $disponiveis[$chave];
    }

    /**
     * Retorna a série atual (a próxima que receberá um número).
     */
    public function getSerieAtual(): int
    {
        return $this->getProximaSerie();
    }

    /**
     * Retorna o total de números da sorte distribuídos.
     */
    public function getTotalDistribuidos(): int
    {
        return NumeroDaSorte::count();
    }
}

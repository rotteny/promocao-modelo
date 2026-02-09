<?php

namespace Database\Seeders;

use App\Jobs\ProcessarCupomFiscal;
use App\Models\CupomFiscal;
use App\Models\ItemCupom;
use App\Models\Participante;
use App\Models\ProdutoParticipante;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder para simulação da promoção com dados em massa.
 *
 * Cria ~500 participantes com 0 a 10 cupons cada,
 * cada cupom com 1 a 5 itens aleatórios (produtos participantes).
 *
 * Todas as datas (cadastro do participante, compra e cadastro do cupom)
 * estão dentro do período de vigência da promoção: 01/01/2026 a 31/12/2026 23:59:59.
 *
 * Todos os cupons ficam com status "validado" para serem
 * processados pelo worker da fila numeros-da-sorte.
 *
 * Uso: php artisan db:seed --class=SimulacaoPromocaoSeeder
 */
class SimulacaoPromocaoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Iniciando simulação da promoção...');

        // Garante que existem produtos participantes
        $produtos = ProdutoParticipante::all();
        if ($produtos->isEmpty()) {
            $this->command->error('Nenhum produto participante encontrado. Execute o DatabaseSeeder primeiro.');
            return;
        }

        $totalParticipantes = 520;
        $totalCupons = 0;
        $totalItens = 0;
        $batchSize = 100;

        // Período de vigência da promoção: início fixo, fim limitado a hoje
        $inicioPromocao = Carbon::create(2026, 1, 1, 0, 0, 0);
        $fimPromocao = Carbon::create(2026, 12, 31, 23, 59, 59);
        $agora = now();

        // O limite real é o menor entre o fim da promoção e agora
        $limiteReal = $agora->lt($fimPromocao) ? $agora->copy() : $fimPromocao->copy();

        // Participantes podem se cadastrar até 2 dias antes do limite,
        // para garantir espaço para cupons serem cadastrados depois
        $fimCadastroParticipante = $limiteReal->copy()->subDays(2);

        // Se o início da promoção é no futuro, não há como gerar dados
        if ($inicioPromocao->gt($limiteReal)) {
            $this->command->error('A promoção ainda não começou. Não é possível gerar dados de simulação.');
            return;
        }

        $this->command->info("Criando {$totalParticipantes} participantes com cupons aleatórios...");
        $this->command->info("Período: {$inicioPromocao->format('d/m/Y H:i')} a {$limiteReal->format('d/m/Y H:i')}");
        $bar = $this->command->getOutput()->createProgressBar($totalParticipantes);
        $bar->start();

        for ($lote = 0; $lote < ceil($totalParticipantes / $batchSize); $lote++) {
            $qtdLote = min($batchSize, $totalParticipantes - ($lote * $batchSize));

            DB::transaction(function () use ($qtdLote, $produtos, &$totalCupons, &$totalItens, $bar, $inicioPromocao, $limiteReal, $fimCadastroParticipante) {
                for ($i = 0; $i < $qtdLote; $i++) {
                    // Data de cadastro do participante: dentro do período da promoção
                    $dataCadastroParticipante = Carbon::createFromTimestamp(
                        rand($inicioPromocao->timestamp, $fimCadastroParticipante->timestamp)
                    );

                    // Cria o participante com created_at/updated_at distribuído
                    $participante = Participante::factory()->create([
                        'created_at' => $dataCadastroParticipante,
                        'updated_at' => $dataCadastroParticipante,
                    ]);

                    // Cada participante recebe entre 0 e 10 cupons
                    $qtdCupons = rand(0, 10);

                    // Gera datas de cupons em ordem cronológica após o cadastro do participante
                    $datasCupons = [];
                    for ($c = 0; $c < $qtdCupons; $c++) {
                        // A data de compra deve ser >= data de cadastro do participante
                        // e <= limite real (com margem para o cadastro posterior)
                        $minimoCompra = $dataCadastroParticipante->copy();
                        $maximoCompra = $limiteReal->copy()->subHours(2); // margem para cadastro do cupom

                        if ($minimoCompra->gte($maximoCompra)) {
                            $minimoCompra = $maximoCompra->copy()->subHour();
                        }

                        $dataCompra = Carbon::createFromTimestamp(
                            rand($minimoCompra->timestamp, $maximoCompra->timestamp)
                        );

                        $datasCupons[] = $dataCompra;
                    }

                    // Ordena as datas para criar os cupons em sequência cronológica
                    usort($datasCupons, fn ($a, $b) => $a->timestamp <=> $b->timestamp);

                    foreach ($datasCupons as $dataCompra) {
                        // Cada cupom tem entre 1 e 5 itens
                        $qtdItens = rand(1, 5);
                        $produtosSelecionados = $produtos->random(min($qtdItens, $produtos->count()));

                        $valorTotal = 0;
                        $itensParaCriar = [];

                        foreach ($produtosSelecionados as $produto) {
                            $quantidade = rand(1, 5);
                            $valorUnitario = round(rand(300, 8000) / 100, 2);
                            $subtotal = round($quantidade * $valorUnitario, 2);
                            $valorTotal += $subtotal;

                            $itensParaCriar[] = [
                                'produto_participante_id' => $produto->id,
                                'quantidade' => $quantidade,
                                'valor_unitario' => $valorUnitario,
                                'subtotal' => $subtotal,
                            ];
                        }

                        // O created_at do cupom é entre a data de compra e +24h depois
                        // (simula o cadastro feito pouco após a compra)
                        $dataCadastroCupom = $dataCompra->copy()->addMinutes(rand(10, 1440));

                        // Garante que não ultrapasse o limite (hoje)
                        if ($dataCadastroCupom->gt($limiteReal)) {
                            $dataCadastroCupom = $limiteReal->copy()->subMinutes(rand(1, 60));
                        }

                        $cupom = CupomFiscal::create([
                            'numero' => (string) rand(100000, 999999),
                            'cnpj_loja' => fake()->numerify('##############'),
                            'data_compra' => $dataCompra->toDateString(),
                            'valor_total' => $valorTotal,
                            'status' => CupomFiscal::STATUS_VALIDADO,
                            'participante_id' => $participante->id,
                            'created_at' => $dataCadastroCupom,
                            'updated_at' => $dataCadastroCupom,
                        ]);

                        foreach ($itensParaCriar as $item) {
                            ItemCupom::create(array_merge($item, [
                                'cupom_fiscal_id' => $cupom->id,
                            ]));
                            $totalItens++;
                        }

                        ProcessarCupomFiscal::dispatch($cupom);
                        $totalCupons++;
                    }

                    $bar->advance();
                }
            });
        }

        $bar->finish();
        $this->command->newLine(2);

        // Estatísticas finais
        $this->command->info('=== Simulação Concluída ===');

        $primeiroParticipante = Participante::orderBy('created_at')->first();
        $ultimoParticipante = Participante::orderBy('created_at', 'desc')->first();
        $primeiroCupom = CupomFiscal::orderBy('created_at')->first();
        $ultimoCupom = CupomFiscal::orderBy('created_at', 'desc')->first();

        $this->command->table(
            ['Métrica', 'Quantidade'],
            [
                ['Participantes criados', number_format($totalParticipantes, 0, ',', '.')],
                ['Cupons fiscais criados', number_format($totalCupons, 0, ',', '.')],
                ['Itens de cupom criados', number_format($totalItens, 0, ',', '.')],
                ['Jobs na fila', number_format(DB::table('jobs')->where('queue', 'numeros-da-sorte')->count(), 0, ',', '.')],
                ['Valor total em compras', 'R$ ' . number_format(CupomFiscal::sum('valor_total'), 2, ',', '.')],
                ['Primeiro cadastro', $primeiroParticipante?->created_at?->format('d/m/Y H:i') ?? '-'],
                ['Último cadastro', $ultimoParticipante?->created_at?->format('d/m/Y H:i') ?? '-'],
                ['Primeiro cupom', $primeiroCupom?->created_at?->format('d/m/Y H:i') ?? '-'],
                ['Último cupom', $ultimoCupom?->created_at?->format('d/m/Y H:i') ?? '-'],
            ]
        );

        $this->command->newLine();
        $this->command->warn('Para processar os cupons e distribuir os números da sorte, inicie o worker:');
        $this->command->line('  php artisan queue:work --queue=numeros-da-sorte --tries=1 --timeout=120');
    }
}

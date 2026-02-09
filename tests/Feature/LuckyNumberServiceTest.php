<?php

namespace Tests\Feature;

use App\Models\CupomFiscal;
use App\Models\ItemCupom;
use App\Models\NumeroDaSorte;
use App\Models\Participante;
use App\Models\ProdutoParticipante;
use App\Models\Setting;
use App\Services\LuckyNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LuckyNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    private LuckyNumberService $service;
    private Participante $participante;
    private ProdutoParticipante $produtoNormal;
    private ProdutoParticipante $produtoBonus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LuckyNumberService();

        $this->participante = Participante::create([
            'name' => 'Teste Participante',
            'cpf' => '123.456.789-00',
            'email' => 'teste@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->produtoNormal = ProdutoParticipante::create([
            'descricao' => 'Produto Normal', 'bonus' => false,
        ]);

        $this->produtoBonus = ProdutoParticipante::create([
            'descricao' => 'Produto Bônus', 'bonus' => true,
        ]);

        Setting::setValue('valor_por_numero', '20', 'Valor em reais por número da sorte');
    }

    /**
     * Cria um cupom com itens de produtos normais e/ou bônus.
     */
    private function criarCupom(float $valorNormal, float $valorBonus = 0): CupomFiscal
    {
        $valorTotal = $valorNormal + $valorBonus;

        $cupom = CupomFiscal::create([
            'numero' => 'CUPOM-' . uniqid(),
            'cnpj_loja' => '12345678000199',
            'data_compra' => now()->toDateString(),
            'valor_total' => $valorTotal,
            'status' => CupomFiscal::STATUS_VALIDADO,
            'participante_id' => $this->participante->id,
        ]);

        if ($valorNormal > 0) {
            ItemCupom::create([
                'cupom_fiscal_id' => $cupom->id,
                'produto_participante_id' => $this->produtoNormal->id,
                'quantidade' => 1,
                'valor_unitario' => $valorNormal,
                'subtotal' => $valorNormal,
            ]);
        }

        if ($valorBonus > 0) {
            ItemCupom::create([
                'cupom_fiscal_id' => $cupom->id,
                'produto_participante_id' => $this->produtoBonus->id,
                'quantidade' => 1,
                'valor_unitario' => $valorBonus,
                'subtotal' => $valorBonus,
            ]);
        }

        return $cupom->fresh();
    }

    // ===========================
    // Testes de cálculo de quantidade
    // ===========================

    #[Test]
    public function calcula_zero_numeros_para_valor_abaixo_de_20(): void
    {
        $cupom = $this->criarCupom(19.99);
        $this->assertEquals(0, $this->service->calcularQuantidadeNumeros($cupom));
    }

    #[Test]
    public function calcula_um_numero_para_valor_de_20_sem_bonus(): void
    {
        $cupom = $this->criarCupom(20.00);
        $this->assertEquals(1, $this->service->calcularQuantidadeNumeros($cupom));
    }

    #[Test]
    public function calcula_numeros_corretos_para_valor_maior_sem_bonus(): void
    {
        $cupom = $this->criarCupom(65.00);
        // floor(65/20) = 3 base, sem bônus
        $this->assertEquals(3, $this->service->calcularQuantidadeNumeros($cupom));
    }

    #[Test]
    public function produto_bonus_conta_dobrado(): void
    {
        // R$ 40 normal + R$ 40 bônus = R$ 80 total
        $cupom = $this->criarCupom(40.00, 40.00);

        // Base: floor(80/20) = 4
        // Bônus: floor(40/20) = 2
        // Total: 4 + 2 = 6
        $this->assertEquals(6, $this->service->calcularQuantidadeNumeros($cupom));
    }

    #[Test]
    public function bonus_proporcional_ao_valor_do_produto_bonus(): void
    {
        // R$ 0 normal + R$ 60 bônus = R$ 60 total
        $cupom = $this->criarCupom(0, 60.00);

        // Base: floor(60/20) = 3
        // Bônus: floor(60/20) = 3
        // Total: 3 + 3 = 6 (produtos bônus contam dobrado)
        $this->assertEquals(6, $this->service->calcularQuantidadeNumeros($cupom));
    }

    #[Test]
    public function bonus_respeita_fracao_sem_arredondar(): void
    {
        // R$ 30 normal + R$ 25 bônus = R$ 55 total
        $cupom = $this->criarCupom(30.00, 25.00);

        // Base: floor(55/20) = 2
        // Bônus: floor(25/20) = 1
        // Total: 2 + 1 = 3
        $this->assertEquals(3, $this->service->calcularQuantidadeNumeros($cupom));
    }

    #[Test]
    public function sem_bonus_quando_valor_bonus_menor_que_20(): void
    {
        // R$ 40 normal + R$ 15 bônus = R$ 55 total
        $cupom = $this->criarCupom(40.00, 15.00);

        // Base: floor(55/20) = 2
        // Bônus: floor(15/20) = 0
        // Total: 2 + 0 = 2
        $this->assertEquals(2, $this->service->calcularQuantidadeNumeros($cupom));
    }

    #[Test]
    public function multiplos_produtos_bonus_somam_valor(): void
    {
        $cupom = CupomFiscal::create([
            'numero' => 'CUPOM-' . uniqid(),
            'cnpj_loja' => '12345678000199',
            'data_compra' => now()->toDateString(),
            'valor_total' => 80.00,
            'status' => CupomFiscal::STATUS_VALIDADO,
            'participante_id' => $this->participante->id,
        ]);

        $produtoBonus2 = ProdutoParticipante::create([
            'descricao' => 'Outro Bônus', 'bonus' => true,
        ]);

        // Produto normal: R$ 20
        ItemCupom::create([
            'cupom_fiscal_id' => $cupom->id,
            'produto_participante_id' => $this->produtoNormal->id,
            'quantidade' => 1, 'valor_unitario' => 20, 'subtotal' => 20,
        ]);
        // Produto bônus 1: R$ 30
        ItemCupom::create([
            'cupom_fiscal_id' => $cupom->id,
            'produto_participante_id' => $this->produtoBonus->id,
            'quantidade' => 1, 'valor_unitario' => 30, 'subtotal' => 30,
        ]);
        // Produto bônus 2: R$ 30
        ItemCupom::create([
            'cupom_fiscal_id' => $cupom->id,
            'produto_participante_id' => $produtoBonus2->id,
            'quantidade' => 1, 'valor_unitario' => 30, 'subtotal' => 30,
        ]);

        $cupom->refresh();

        // Base: floor(80/20) = 4
        // Bônus: floor((30+30)/20) = floor(60/20) = 3
        // Total: 4 + 3 = 7
        $this->assertEquals(7, $this->service->calcularQuantidadeNumeros($cupom));
    }

    // ===========================
    // Testes de distribuição round-robin
    // ===========================

    #[Test]
    public function primeiro_numero_vai_para_serie_0(): void
    {
        $cupom = $this->criarCupom(20.00);
        $numeros = $this->service->gerarNumeros($cupom);

        $this->assertCount(1, $numeros);
        $this->assertEquals(0, $numeros->first()->serie);
    }

    #[Test]
    public function dois_numeros_distribuidos_em_series_consecutivas(): void
    {
        $cupom = $this->criarCupom(40.00);
        $numeros = $this->service->gerarNumeros($cupom);

        $this->assertCount(2, $numeros);
        $this->assertEquals(0, $numeros[0]->serie);
        $this->assertEquals(1, $numeros[1]->serie);
    }

    #[Test]
    public function tres_numeros_distribuidos_em_tres_series(): void
    {
        $cupom = $this->criarCupom(60.00);
        $numeros = $this->service->gerarNumeros($cupom);

        $this->assertCount(3, $numeros);
        $this->assertEquals(0, $numeros[0]->serie);
        $this->assertEquals(1, $numeros[1]->serie);
        $this->assertEquals(2, $numeros[2]->serie);
    }

    #[Test]
    public function segundo_cupom_continua_da_serie_seguinte(): void
    {
        // Primeiro cupom: 2 números -> séries 0 e 1
        $cupom1 = $this->criarCupom(40.00);
        $numeros1 = $this->service->gerarNumeros($cupom1);

        $this->assertEquals(0, $numeros1[0]->serie);
        $this->assertEquals(1, $numeros1[1]->serie);

        // Segundo cupom: 3 números -> deve continuar na série 2, 3, 4
        $cupom2 = $this->criarCupom(60.00);
        $numeros2 = $this->service->gerarNumeros($cupom2);

        $this->assertCount(3, $numeros2);
        $this->assertEquals(2, $numeros2[0]->serie);
        $this->assertEquals(3, $numeros2[1]->serie);
        $this->assertEquals(4, $numeros2[2]->serie);
    }

    #[Test]
    public function distribuicao_cicla_apos_ultima_serie(): void
    {
        // Cria 10 números para ocupar uma posição em cada série (0..9)
        $cupom1 = $this->criarCupom(200.00); // 10 números: séries 0,1,2,3,4,5,6,7,8,9
        $numeros1 = $this->service->gerarNumeros($cupom1);

        $this->assertCount(10, $numeros1);
        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals($i, $numeros1[$i]->serie);
        }

        // Próximo cupom: 2 números -> deve reiniciar na série 0 e depois 1
        $cupom2 = $this->criarCupom(40.00);
        $numeros2 = $this->service->gerarNumeros($cupom2);

        $this->assertCount(2, $numeros2);
        $this->assertEquals(0, $numeros2[0]->serie);
        $this->assertEquals(1, $numeros2[1]->serie);
    }

    #[Test]
    public function numeros_sao_aleatorios_dentro_da_serie(): void
    {
        $cupom = $this->criarCupom(40.00);
        $numeros = $this->service->gerarNumeros($cupom);

        foreach ($numeros as $ns) {
            $this->assertGreaterThanOrEqual(0, $ns->numero);
            $this->assertLessThanOrEqual(9999, $ns->numero);
        }
    }

    #[Test]
    public function numeros_sao_unicos_dentro_de_cada_serie(): void
    {
        // Gera 20 números: 2 por série (20 números = R$ 400, séries 0..9 duas vezes)
        $cupom = $this->criarCupom(400.00);
        $numeros = $this->service->gerarNumeros($cupom);

        $this->assertCount(20, $numeros);

        // Agrupa por série e verifica unicidade
        $porSerie = $numeros->groupBy('serie');
        foreach ($porSerie as $serie => $numerosNaSerie) {
            $valores = $numerosNaSerie->pluck('numero')->toArray();
            $this->assertCount(count($valores), array_unique($valores),
                "Números duplicados encontrados na série {$serie}");
        }
    }

    #[Test]
    public function gera_numeros_com_bonus_proporcional(): void
    {
        // R$ 20 normal + R$ 40 bônus = R$ 60 total
        $cupom = $this->criarCupom(20.00, 40.00);
        $numeros = $this->service->gerarNumeros($cupom);

        // Base: floor(60/20) = 3, Bônus: floor(40/20) = 2, Total: 5
        $this->assertCount(5, $numeros);

        // Séries: 0, 1, 2, 3, 4
        for ($i = 0; $i < 5; $i++) {
            $this->assertEquals($i, $numeros[$i]->serie);
        }
    }

    // ===========================
    // Testes de estado e limites
    // ===========================

    #[Test]
    public function nao_gera_numeros_para_cupom_com_valor_insuficiente(): void
    {
        $cupom = $this->criarCupom(15.00);
        $numeros = $this->service->gerarNumeros($cupom);
        $this->assertCount(0, $numeros);
    }

    #[Test]
    public function numeros_sao_salvos_no_banco_de_dados(): void
    {
        $cupom = $this->criarCupom(40.00);
        $this->service->gerarNumeros($cupom);
        $this->assertDatabaseCount('numeros_da_sorte', 2);
    }

    #[Test]
    public function proxima_serie_comeca_em_0_quando_vazio(): void
    {
        $this->assertEquals(0, $this->service->getProximaSerie());
        $this->assertEquals(0, $this->service->getSerieAtual());
    }

    #[Test]
    public function proxima_serie_avanca_apos_distribuicao(): void
    {
        $cupom = $this->criarCupom(60.00); // 3 números: séries 0, 1, 2
        $this->service->gerarNumeros($cupom);

        // Última distribuída foi série 2, próxima deve ser 3
        $this->assertEquals(3, $this->service->getProximaSerie());
    }

    #[Test]
    public function proxima_serie_cicla_apos_serie_9(): void
    {
        // Gera 10 números para passar por todas as séries
        $cupom = $this->criarCupom(200.00);
        $this->service->gerarNumeros($cupom);

        // Última distribuída foi série 9, próxima deve ser 0
        $this->assertEquals(0, $this->service->getProximaSerie());
    }

    #[Test]
    public function retorna_total_distribuidos(): void
    {
        $cupom = $this->criarCupom(100.00);
        $this->service->gerarNumeros($cupom);
        $this->assertEquals(5, $this->service->getTotalDistribuidos());
    }

    #[Test]
    public function valor_por_numero_usa_configuracao(): void
    {
        Setting::setValue('valor_por_numero', '10');
        $this->assertEquals(10.0, $this->service->getValorPorNumero());

        $cupom = $this->criarCupom(30.00);
        // floor(30/10) = 3
        $this->assertEquals(3, $this->service->calcularQuantidadeNumeros($cupom));
    }

    #[Test]
    public function pula_serie_cheia_e_continua_na_proxima(): void
    {
        // Preenche completamente a série 0
        for ($i = 0; $i < LuckyNumberService::NUMEROS_POR_SERIE; $i++) {
            NumeroDaSorte::create([
                'numero' => $i,
                'serie' => 0,
                'participante_id' => $this->participante->id,
                'cupom_fiscal_id' => 1,
            ]);
        }

        // Força a próxima série a ser 0 (criando o último na série 9)
        NumeroDaSorte::orderByDesc('id')->first()->update(['serie' => 9]);

        // A próxima série seria 0, mas está cheia, deve ir para 1
        $cupom = $this->criarCupom(20.00);
        $numeros = $this->service->gerarNumeros($cupom);

        $this->assertCount(1, $numeros);
        $this->assertEquals(1, $numeros->first()->serie);
    }
}

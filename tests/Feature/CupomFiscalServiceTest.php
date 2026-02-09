<?php

namespace Tests\Feature;

use App\Jobs\ProcessarCupomFiscal;
use App\Models\CupomFiscal;
use App\Models\Participante;
use App\Models\ProdutoParticipante;
use App\Models\Setting;
use App\Services\CupomFiscalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CupomFiscalServiceTest extends TestCase
{
    use RefreshDatabase;

    private CupomFiscalService $service;
    private Participante $participante;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CupomFiscalService::class);

        $this->participante = Participante::create([
            'name' => 'Teste',
            'cpf' => '111.222.333-44',
            'email' => 'teste@cupom.com',
            'password' => bcrypt('password'),
        ]);

        Setting::setValue('valor_por_numero', '20');
    }

    #[Test]
    public function cria_cupom_com_itens_e_despacha_job(): void
    {
        Queue::fake();

        $produto = ProdutoParticipante::create([
            'descricao' => 'Produto A', 'bonus' => false,
        ]);

        $dados = [
            'numero' => 'CF-001',
            'cnpj_loja' => '12345678000199',
            'chave_acesso' => null,
            'data_compra' => now()->toDateString(),
            'itens' => [
                [
                    'produto_participante_id' => $produto->id,
                    'quantidade' => 2,
                    'valor_unitario' => 25.00,
                ],
            ],
        ];

        $cupom = $this->service->criarCupom($this->participante->id, $dados);

        $this->assertEquals('CF-001', $cupom->numero);
        $this->assertEquals(50.00, $cupom->valor_total);
        $this->assertEquals(CupomFiscal::STATUS_VALIDADO, $cupom->status);
        $this->assertCount(1, $cupom->itens);

        // Verifica que o job foi despachado
        Queue::assertPushed(ProcessarCupomFiscal::class, function ($job) use ($cupom) {
            return $job->cupom->id === $cupom->id;
        });
    }

    #[Test]
    public function cupom_sem_chave_acesso_e_validado_automaticamente(): void
    {
        Queue::fake();

        $produto = ProdutoParticipante::create([
            'descricao' => 'Produto', 'bonus' => false,
        ]);

        $dados = [
            'numero' => 'CF-AUTO',
            'cnpj_loja' => '12345678000199',
            'chave_acesso' => null,
            'data_compra' => now()->toDateString(),
            'itens' => [
                ['produto_participante_id' => $produto->id, 'quantidade' => 1, 'valor_unitario' => 40.00],
            ],
        ];

        $cupom = $this->service->criarCupom($this->participante->id, $dados);

        $this->assertEquals(CupomFiscal::STATUS_VALIDADO, $cupom->status);
        Queue::assertPushed(ProcessarCupomFiscal::class);
    }

    #[Test]
    public function cupom_com_chave_invalida_e_rejeitado(): void
    {
        Queue::fake();

        $produto = ProdutoParticipante::create([
            'descricao' => 'Produto', 'bonus' => false,
        ]);

        // Chave com letras (inválida para o MockInvoiceValidator)
        $dados = [
            'numero' => 'CF-REJEITADO',
            'cnpj_loja' => '12345678000199',
            'chave_acesso' => str_repeat('A', 44),
            'data_compra' => now()->toDateString(),
            'itens' => [
                ['produto_participante_id' => $produto->id, 'quantidade' => 1, 'valor_unitario' => 40.00],
            ],
        ];

        $cupom = $this->service->criarCupom($this->participante->id, $dados);

        $this->assertEquals(CupomFiscal::STATUS_REJEITADO, $cupom->status);
        Queue::assertNotPushed(ProcessarCupomFiscal::class);
    }

    #[Test]
    public function calcula_valor_total_a_partir_dos_itens(): void
    {
        Queue::fake();

        $produto = ProdutoParticipante::create([
            'descricao' => 'Produto', 'bonus' => false,
        ]);

        $dados = [
            'numero' => 'CF-003',
            'cnpj_loja' => '12345678000199',
            'data_compra' => now()->toDateString(),
            'itens' => [
                ['produto_participante_id' => $produto->id, 'quantidade' => 3, 'valor_unitario' => 10.00],
                ['produto_participante_id' => $produto->id, 'quantidade' => 2, 'valor_unitario' => 25.00],
            ],
        ];

        $cupom = $this->service->criarCupom($this->participante->id, $dados);

        // (3 * 10) + (2 * 25) = 30 + 50 = 80
        $this->assertEquals(80.00, $cupom->valor_total);

        // Verifica que o job foi despachado na fila correta
        Queue::assertPushed(ProcessarCupomFiscal::class, function ($job) {
            return $job->queue === 'numeros-da-sorte';
        });
    }

    #[Test]
    public function status_do_cupom_fica_validado_ate_processamento(): void
    {
        Queue::fake();

        $produto = ProdutoParticipante::create([
            'descricao' => 'Produto', 'bonus' => false,
        ]);

        $dados = [
            'numero' => 'CF-STATUS',
            'cnpj_loja' => '12345678000199',
            'data_compra' => now()->toDateString(),
            'itens' => [
                ['produto_participante_id' => $produto->id, 'quantidade' => 1, 'valor_unitario' => 40.00],
            ],
        ];

        $cupom = $this->service->criarCupom($this->participante->id, $dados);

        // Status deve ser 'validado' (aguardando processamento assíncrono)
        $this->assertEquals(CupomFiscal::STATUS_VALIDADO, $cupom->status);

        // Não deve ter números ainda (processamento é assíncrono)
        $this->assertCount(0, $cupom->numerosDaSorte);
    }

    #[Test]
    public function itens_do_cupom_sao_salvos_com_subtotal_correto(): void
    {
        Queue::fake();

        $produto = ProdutoParticipante::create([
            'descricao' => 'Produto X', 'bonus' => false,
        ]);

        $dados = [
            'numero' => 'CF-ITENS',
            'cnpj_loja' => '12345678000199',
            'data_compra' => now()->toDateString(),
            'itens' => [
                ['produto_participante_id' => $produto->id, 'quantidade' => 3, 'valor_unitario' => 15.50],
            ],
        ];

        $cupom = $this->service->criarCupom($this->participante->id, $dados);

        $this->assertCount(1, $cupom->itens);
        $item = $cupom->itens->first();
        $this->assertEquals(3, $item->quantidade);
        $this->assertEquals(15.50, $item->valor_unitario);
        $this->assertEquals(46.50, $item->subtotal); // 3 * 15.50
    }
}

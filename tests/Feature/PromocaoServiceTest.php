<?php

namespace Tests\Feature;

use App\Models\NumeroDaSorte;
use App\Models\Participante;
use App\Models\Setting;
use App\Models\User;
use App\Services\LuckyNumberService;
use App\Services\PromocaoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PromocaoServiceTest extends TestCase
{
    use RefreshDatabase;

    private PromocaoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PromocaoService::class);

        Setting::setValue('campanha_encerrada', 'false');
        Setting::setValue('campanha_motivo_encerramento', '');
    }

    #[Test]
    public function promocao_ativa_quando_dentro_do_periodo(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDay()->format('Y-m-d H:i:s'));

        $this->assertTrue($this->service->isAtiva());
        $this->assertFalse($this->service->isEncerrada());
        $this->assertFalse($this->service->isAguardando());
        $this->assertEquals(PromocaoService::STATUS_ATIVA, $this->service->getStatus());
    }

    #[Test]
    public function promocao_aguardando_quando_antes_do_inicio(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->addDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDays(30)->format('Y-m-d H:i:s'));

        $this->assertFalse($this->service->isAtiva());
        $this->assertFalse($this->service->isEncerrada());
        $this->assertTrue($this->service->isAguardando());
        $this->assertEquals(PromocaoService::STATUS_AGUARDANDO, $this->service->getStatus());
    }

    #[Test]
    public function promocao_encerrada_por_prazo_quando_apos_fim(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDays(30)->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->subDay()->format('Y-m-d H:i:s'));

        $this->assertFalse($this->service->isAtiva());
        $this->assertTrue($this->service->isEncerrada());
        $this->assertEquals(PromocaoService::STATUS_ENCERRADA_PRAZO, $this->service->getStatus());
    }

    #[Test]
    public function promocao_encerrada_manualmente(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDay()->format('Y-m-d H:i:s'));
        Setting::setValue('campanha_encerrada', 'true');
        Setting::setValue('campanha_motivo_encerramento', 'manual');

        $this->assertFalse($this->service->isAtiva());
        $this->assertTrue($this->service->isEncerrada());
        $this->assertEquals(PromocaoService::STATUS_ENCERRADA_MANUAL, $this->service->getStatus());
    }

    #[Test]
    public function promocao_encerrada_por_esgotamento(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDay()->format('Y-m-d H:i:s'));
        Setting::setValue('campanha_encerrada', 'true');
        Setting::setValue('campanha_motivo_encerramento', 'esgotamento');

        $this->assertFalse($this->service->isAtiva());
        $this->assertTrue($this->service->isEncerrada());
        $this->assertEquals(PromocaoService::STATUS_ENCERRADA_ESGOTAMENTO, $this->service->getStatus());
    }

    #[Test]
    public function mensagem_status_aguardando(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->addDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDays(30)->format('Y-m-d H:i:s'));

        $mensagem = $this->service->getMensagemStatus();
        $this->assertStringContainsString('ainda não começou', $mensagem);
    }

    #[Test]
    public function mensagem_status_ativa(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDay()->format('Y-m-d H:i:s'));

        $mensagem = $this->service->getMensagemStatus();
        $this->assertStringContainsString('está ativa', $mensagem);
    }

    #[Test]
    public function mensagem_status_encerrada_prazo(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDays(30)->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->subDay()->format('Y-m-d H:i:s'));

        $mensagem = $this->service->getMensagemStatus();
        $this->assertStringContainsString('período da promoção foi encerrado', $mensagem);
    }

    #[Test]
    public function numeros_disponiveis_calcula_corretamente(): void
    {
        $capacidadeTotal = LuckyNumberService::TOTAL_SERIES * LuckyNumberService::NUMEROS_POR_SERIE;
        $this->assertEquals($capacidadeTotal, $this->service->getNumerosDisponiveis());
        $this->assertFalse($this->service->isNumerosEsgotados());
    }

    #[Test]
    public function to_array_retorna_estrutura_publica_sem_dados_sensiveis(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDay()->format('Y-m-d H:i:s'));

        $dados = $this->service->toArray();

        $this->assertArrayHasKey('status', $dados);
        $this->assertArrayHasKey('ativa', $dados);
        $this->assertArrayHasKey('mensagem', $dados);
        $this->assertArrayHasKey('servidor_agora', $dados);
        $this->assertArrayNotHasKey('data_inicio', $dados);
        $this->assertArrayNotHasKey('data_fim', $dados);
        $this->assertArrayNotHasKey('numeros_distribuidos', $dados);
        $this->assertArrayNotHasKey('numeros_disponiveis', $dados);
        $this->assertArrayNotHasKey('capacidade_total', $dados);
        $this->assertTrue($dados['ativa']);
        $this->assertEquals(PromocaoService::STATUS_ATIVA, $dados['status']);
    }

    #[Test]
    public function to_admin_array_retorna_estrutura_completa(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDay()->format('Y-m-d H:i:s'));

        $dados = $this->service->toAdminArray();

        $this->assertArrayHasKey('status', $dados);
        $this->assertArrayHasKey('ativa', $dados);
        $this->assertArrayHasKey('mensagem', $dados);
        $this->assertArrayHasKey('data_inicio', $dados);
        $this->assertArrayHasKey('data_fim', $dados);
        $this->assertArrayHasKey('numeros_distribuidos', $dados);
        $this->assertArrayHasKey('numeros_disponiveis', $dados);
        $this->assertArrayHasKey('capacidade_total', $dados);
        $this->assertArrayHasKey('servidor_agora', $dados);
        $this->assertTrue($dados['ativa']);
        $this->assertEquals(PromocaoService::STATUS_ATIVA, $dados['status']);
    }

    #[Test]
    public function verificar_esgotamento_notifica_admins(): void
    {
        Notification::fake();

        Setting::setValue('data_inicio', Carbon::now()->subDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDay()->format('Y-m-d H:i:s'));

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@promo.com',
            'password' => bcrypt('admin'),
        ]);

        // Sem esgotamento: deve retornar false
        $this->assertFalse($this->service->verificarEsgotamento());
        $this->assertNotEquals('true', Setting::getValue('campanha_encerrada'));

        Notification::assertNotSentTo($admin, \App\Notifications\CampanhaEncerrada::class);
    }

    #[Test]
    public function encerramento_manual_tem_prioridade_sobre_periodo_ativo(): void
    {
        Setting::setValue('data_inicio', Carbon::now()->subDay()->format('Y-m-d H:i:s'));
        Setting::setValue('data_fim', Carbon::now()->addDay()->format('Y-m-d H:i:s'));
        Setting::setValue('campanha_encerrada', 'true');
        Setting::setValue('campanha_motivo_encerramento', 'manual');

        // Mesmo com o período ativo, a campanha deve estar encerrada
        $this->assertFalse($this->service->isAtiva());
        $this->assertTrue($this->service->isEncerrada());
    }

    #[Test]
    public function data_inicio_e_fim_retornam_carbon(): void
    {
        Setting::setValue('data_inicio', '2025-06-01 10:00:00');
        Setting::setValue('data_fim', '2025-12-31 23:59:59');

        $this->assertInstanceOf(Carbon::class, $this->service->getDataInicio());
        $this->assertInstanceOf(Carbon::class, $this->service->getDataFim());
        $this->assertEquals('2025-06-01 10:00:00', $this->service->getDataInicio()->format('Y-m-d H:i:s'));
        $this->assertEquals('2025-12-31 23:59:59', $this->service->getDataFim()->format('Y-m-d H:i:s'));
    }
}

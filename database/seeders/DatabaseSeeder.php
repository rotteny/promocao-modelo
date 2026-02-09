<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Participante;
use App\Models\ProdutoParticipante;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedSettings();
        $this->seedAdmin();
        $this->seedProdutos();
        $this->seedParticipanteExemplo();
        $this->seedFaqs();
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'valor_por_numero', 'value' => '20', 'description' => 'Valor em R$ para ganhar 1 número da sorte'],
            ['key' => 'bonus_numeros', 'value' => 'proporcional', 'description' => 'Bônus proporcional: +1 número extra a cada R$ 20 em produtos bônus'],
            ['key' => 'data_inicio', 'value' => '2026-01-01 00:00:00', 'description' => 'Data e hora de início da promoção'],
            ['key' => 'data_fim', 'value' => '2026-12-31 23:59:59', 'description' => 'Data e hora de término da promoção'],
            ['key' => 'nome_promocao', 'value' => 'Promoção Modelo 2026', 'description' => 'Nome da campanha promocional'],
            ['key' => 'total_series', 'value' => '10', 'description' => 'Total de séries (0 a 9)'],
            ['key' => 'numeros_por_serie', 'value' => '10000', 'description' => 'Quantidade de números por série (0000 a 9999)'],
            ['key' => 'fila_bloqueada', 'value' => 'false', 'description' => 'Status da fila de processamento (true = bloqueada)'],
            ['key' => 'fila_cupom_erro_id', 'value' => '', 'description' => 'ID do cupom que causou o bloqueio da fila'],
            ['key' => 'campanha_encerrada', 'value' => 'false', 'description' => 'Campanha encerrada (true = encerrada manualmente ou por esgotamento)'],
            ['key' => 'campanha_motivo_encerramento', 'value' => '', 'description' => 'Motivo do encerramento (esgotamento, manual)'],
        ];

        foreach ($settings as $setting) {
            Setting::setValue($setting['key'], $setting['value'], $setting['description']);
        }
    }

    private function seedAdmin(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@promocaomodelo.com.br'],
            [
                'name' => 'Administrador',
                'email' => 'admin@promocaomodelo.com.br',
                'password' => 'admin123',
                'is_super_admin' => true,
                'perm_produtos' => true,
                'perm_faq' => true,
                'perm_configuracoes' => true,
                'perm_encerrar_campanha' => true,
                'ativo' => true,
            ]
        );

        // Garante que o admin existente tenha as permissões
        if (! $admin->wasRecentlyCreated && ! $admin->is_super_admin) {
            $admin->update([
                'is_super_admin' => true,
                'perm_produtos' => true,
                'perm_faq' => true,
                'perm_configuracoes' => true,
                'perm_encerrar_campanha' => true,
                'ativo' => true,
            ]);
        }
    }

    private function seedProdutos(): void
    {
        $produtos = [
            ['descricao' => 'Cerveja Modelo Especial 600ml', 'bonus' => true],
            ['descricao' => 'Cerveja Modelo Especial Lata 350ml', 'bonus' => true],
            ['descricao' => 'Cerveja Modelo Negra 355ml', 'bonus' => true],
            ['descricao' => 'Refrigerante Cola 2L', 'bonus' => false],
            ['descricao' => 'Suco Natural Laranja 1L', 'bonus' => false],
            ['descricao' => 'Água Mineral 500ml', 'bonus' => false],
            ['descricao' => 'Energético Power 250ml', 'bonus' => false],
            ['descricao' => 'Cerveja Modelo Trigo 355ml', 'bonus' => true],
        ];

        foreach ($produtos as $produto) {
            ProdutoParticipante::firstOrCreate(
                ['descricao' => $produto['descricao']],
                $produto
            );
        }
    }

    private function seedParticipanteExemplo(): void
    {
        Participante::firstOrCreate(
            ['email' => 'participante@example.com'],
            [
                'name' => 'João da Silva',
                'cpf' => '111.222.333-44',
                'email' => 'participante@example.com',
                'password' => 'senha123',
                'telefone' => '(11) 98765-4321',
                'endereco' => 'Rua das Flores',
                'numero' => '123',
                'bairro' => 'Centro',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01234-567',
            ]
        );
    }

    private function seedFaqs(): void
    {
        $faqs = [
            [
                'pergunta' => 'Como faço para participar da Promoção Modelo?',
                'resposta' => 'Basta criar sua conta gratuitamente no site com seus dados pessoais (nome, CPF, e-mail e endereço). Após o cadastro, você já pode começar a registrar seus cupons fiscais e acumular números da sorte.',
                'ordem' => 1,
            ],
            [
                'pergunta' => 'Quantos números da sorte eu ganho por cupom?',
                'resposta' => 'Você ganha 1 número da sorte a cada R$ 20,00 em compras de produtos participantes. Além disso, produtos bônus geram números extras: +1 número a cada R$ 20,00 do valor dos produtos bônus. Por exemplo, um cupom de R$ 65,00 com R$ 40,00 em produtos bônus gera 3 números base + 2 bônus = 5 números.',
                'ordem' => 2,
            ],
            [
                'pergunta' => 'O que são produtos bônus?',
                'resposta' => 'Produtos bônus são itens participantes especiais da promoção. Eles contam em dobro para geração de números da sorte: além de participarem da contagem base (1 número a cada R$ 20), eles geram +1 número extra a cada R$ 20 do seu valor. Quanto mais você comprar em produtos bônus, mais números extras você ganha!',
                'ordem' => 3,
            ],
            [
                'pergunta' => 'Como cadastro um cupom fiscal?',
                'resposta' => 'Após fazer login, acesse "Cadastrar Cupom" no menu. Você pode inserir os dados manualmente (número do cupom e itens) ou informar a chave de acesso de 44 dígitos (QR Code) para validação automática.',
                'ordem' => 4,
            ],
            [
                'pergunta' => 'O que significa o status do meu cupom?',
                'resposta' => "Os status possíveis são:\n- Na Fila: seu cupom foi validado e está aguardando a geração dos números da sorte.\n- Processando: seus números estão sendo gerados neste momento.\n- Concluído: seus números da sorte foram gerados com sucesso.\n- Pendente: seu cupom está aguardando verificação.\n- Erro: houve um problema no processamento (será resolvido pela equipe).\n- Rejeitado: seu cupom não foi aprovado na validação.",
                'ordem' => 5,
            ],
            [
                'pergunta' => 'Posso cadastrar mais de uma vez com o mesmo CPF?',
                'resposta' => 'Não. Cada CPF pode ser cadastrado apenas uma vez na promoção. Caso tenha dificuldades com seu cadastro, entre em contato pelo e-mail contato@promocaomodelo.com.br.',
                'ordem' => 6,
            ],
            [
                'pergunta' => 'Como funcionam as séries dos números da sorte?',
                'resposta' => 'Os números são distribuídos em 10 séries (0 a 9), cada uma com 10.000 números (0000 a 9999). As séries são preenchidas sequencialmente e os números dentro de cada série são gerados aleatoriamente.',
                'ordem' => 7,
            ],
            [
                'pergunta' => 'Onde posso consultar meus números da sorte?',
                'resposta' => 'Após fazer login, acesse "Meus Números" no menu principal. Lá você encontra todos os seus números da sorte organizados por série, além do histórico completo de cupons cadastrados.',
                'ordem' => 8,
            ],
            [
                'pergunta' => 'Qual é o período da promoção?',
                'resposta' => 'A promoção possui data e hora de início e término definidas. Cadastros e cupons só são aceitos durante o período ativo. Se estiver preenchendo um formulário e o prazo encerrar, você será notificado automaticamente e a submissão será impedida. Após o encerramento, aguarde a data do sorteio.',
                'ordem' => 9,
            ],
            [
                'pergunta' => 'O que acontece se os números da sorte se esgotarem?',
                'resposta' => 'A promoção possui um total de 100.000 números da sorte (10 séries com 10.000 números cada). Caso todos sejam distribuídos antes da data final, a campanha é encerrada automaticamente e imediatamente. Nenhum novo cadastro ou cupom será aceito. Aguarde a data do sorteio!',
                'ordem' => 10,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['pergunta' => $faq['pergunta']],
                array_merge($faq, ['ativo' => true])
            );
        }
    }
}

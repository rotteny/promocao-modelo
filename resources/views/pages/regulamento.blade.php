@extends('layouts.app')

@section('title', 'Regulamento - Promoção Modelo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-pm-gradient text-white py-3">
                    <h4 class="mb-0"><i class="bi bi-file-text me-2"></i>Regulamento Oficial da Promoção</h4>
                </div>
                <div class="card-body p-4">

                    <div class="alert alert-warning border-warning mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">Promoção Fictícia — Sem Validade Jurídica</h6>
                                <p class="mb-0 small">
                                    Este regulamento faz parte de um <strong>projeto de demonstração para portfólio</strong>.
                                    Trata-se de uma promoção fictícia, sem qualquer validade jurídica, comercial ou legal.
                                    Nenhum dado cadastrado é real. Nenhum prêmio será sorteado ou entregue.
                                </p>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted small mb-4">A participação na presente promoção implica a aceitação total e irrestrita de todos os termos e condições deste Regulamento.</p>

                    <h5 class="fw-bold">1. DO OBJETO</h5>
                    <p>1.1. A "Promoção Modelo" (doravante denominada "Promoção") é uma campanha promocional de caráter comercial que tem por finalidade premiar consumidores participantes por meio de sorteios vinculados a números da sorte, os quais serão atribuídos mediante o cadastro de cupons fiscais referentes a compras realizadas em estabelecimentos participantes durante o período de vigência.</p>

                    <h5 class="fw-bold mt-4">2. DO PERÍODO DE VIGÊNCIA</h5>
                    <p>2.1. A Promoção terá vigência conforme datas de início e término definidas pela organização, as quais poderão ser consultadas na página inicial do sistema.</p>
                    <p>2.2. Somente serão considerados válidos para fins de participação os cupons fiscais cuja <strong>data de compra</strong> esteja compreendida dentro do período de vigência da Promoção.</p>
                    <p>2.3. Cadastros de cupons fiscais com data de compra anterior ao início ou posterior ao término da vigência serão automaticamente rejeitados pelo sistema.</p>

                    <h5 class="fw-bold mt-4">3. DA ELEGIBILIDADE E PARTICIPAÇÃO</h5>
                    <p>3.1. Poderão participar da Promoção pessoas físicas, maiores de 18 (dezoito) anos, devidamente inscritas no Cadastro de Pessoas Físicas (CPF) do Ministério da Fazenda.</p>
                    <p>3.2. É vedada a participação de menores de 18 (dezoito) anos, bem como de funcionários, sócios, diretores e prestadores de serviço da empresa organizadora e de suas coligadas.</p>
                    <p>3.3. Para participar, o consumidor deverá:</p>
                    <ul>
                        <li>Realizar cadastro no site oficial da Promoção, fornecendo dados pessoais válidos e verídicos, incluindo nome completo, CPF, endereço de e-mail, endereço residencial e telefone de contato;</li>
                        <li>Cadastrar cupons fiscais de suas compras, informando obrigatoriamente o número do cupom, o CNPJ do estabelecimento emitente, a data da compra e os itens adquiridos que sejam produtos participantes;</li>
                        <li>Opcionalmente, realizar a leitura do QR Code impresso na nota fiscal para validação automática dos dados junto à Secretaria da Fazenda (Sefaz).</li>
                    </ul>
                    <p>3.4. Cada CPF poderá ser cadastrado uma única vez no sistema. O cadastro é pessoal e intransferível.</p>

                    <h5 class="fw-bold mt-4">4. DA RESPONSABILIDADE SOBRE OS DADOS CADASTRADOS</h5>
                    <p>4.1. <strong>O participante é o único e exclusivo responsável pela veracidade, exatidão e autenticidade de todas as informações e documentos fornecidos no ato do cadastro e no registro dos cupons fiscais.</strong></p>
                    <p>4.2. A organização reserva-se o direito de, a qualquer tempo e a seu exclusivo critério, solicitar ao participante a comprovação dos dados cadastrados, incluindo, mas não se limitando a:</p>
                    <ul>
                        <li>Apresentação da via original do cupom fiscal cadastrado;</li>
                        <li>Documento de identidade com foto e CPF;</li>
                        <li>Comprovante de residência atualizado;</li>
                        <li>Quaisquer outros documentos que a organização julgar necessários para a verificação.</li>
                    </ul>
                    <p>4.3. <strong>Será automaticamente anulada a participação do consumidor, bem como cancelados todos os números da sorte a ele atribuídos, sem direito a qualquer indenização ou compensação, nas seguintes hipóteses:</strong></p>
                    <ul>
                        <li>Impossibilidade de comprovação de qualquer dado cadastrado, incluindo a não apresentação do cupom fiscal original quando solicitado;</li>
                        <li>Constatação de inconsistência, divergência ou inveracidade nas informações cadastrais ou nos dados dos cupons fiscais registrados;</li>
                        <li>Utilização de dados de terceiros, ainda que com consentimento;</li>
                        <li>Cadastro de cupons fiscais adulterados, falsificados ou obtidos por meios fraudulentos;</li>
                        <li>Descumprimento de qualquer disposição prevista neste Regulamento.</li>
                    </ul>
                    <p>4.4. A anulação de que trata o item 4.3 poderá ocorrer a qualquer tempo, inclusive após eventual sorteio, hipótese em que o prêmio porventura contemplado será considerado nulo e sem efeito.</p>

                    <h5 class="fw-bold mt-4">5. DOS NÚMEROS DA SORTE</h5>
                    <p>5.1. Os números da sorte serão distribuídos conforme as seguintes regras:</p>
                    <ul>
                        <li><strong>Regra Base:</strong> A cada R$ 20,00 (vinte reais) em compras de produtos participantes registradas em um único cupom fiscal, o participante fará jus a 1 (um) número da sorte. Valores fracionários não são considerados para fins de cálculo (exemplo: R$ 55,00 em compras gera 2 números da sorte);</li>
                        <li><strong>Bônus:</strong> Produtos participantes expressamente designados como "produtos bônus" pela organização conferem números da sorte adicionais proporcionais ao seu valor. O participante receberá 1 (um) número da sorte adicional a cada R$ 20,00 (vinte reais) em produtos bônus contidos no mesmo cupom fiscal. Os produtos bônus são computados tanto na contagem base quanto na contagem de bônus;</li>
                        <li><strong>Séries:</strong> Os números da sorte são organizados em 10 (dez) séries, numeradas de 0 a 9, cada série contendo 10.000 (dez mil) números, de 0000 a 9999;</li>
                        <li><strong>Distribuição:</strong> Os números são distribuídos entre as séries de forma sequencial e rotativa. Dentro de cada série, a geração dos números é aleatória e sem repetição;</li>
                        <li><strong>Capacidade:</strong> O total máximo de números da sorte disponíveis na Promoção é de 100.000 (cem mil), distribuídos entre todas as séries.</li>
                    </ul>

                    <h5 class="fw-bold mt-4">6. DO ESGOTAMENTO DOS NÚMEROS DA SORTE</h5>
                    <p>6.1. <strong>A Promoção será automaticamente encerrada para novos cadastros quando a totalidade dos 100.000 (cem mil) números da sorte disponíveis tiver sido distribuída.</strong></p>
                    <p>6.2. Na hipótese de o processamento de um cupom fiscal ocorrer quando a quantidade de números da sorte remanescentes for inferior à quantidade a que o participante teria direito, <strong>o participante receberá tão somente a quantidade de números da sorte ainda disponíveis no momento do processamento</strong>, sem que lhe assista qualquer direito a números adicionais, complementação posterior ou indenização de qualquer natureza.</p>
                    <p>6.3. Cupons fiscais cadastrados e ainda não processados no momento do esgotamento serão considerados inválidos para fins de geração de números da sorte, não conferindo ao participante qualquer direito ou expectativa de direito sobre números da sorte adicionais.</p>
                    <p>6.4. O esgotamento dos números da sorte não invalida os números já distribuídos e devidamente atribuídos aos participantes, os quais permanecem válidos para os sorteios previstos.</p>

                    <h5 class="fw-bold mt-4">7. DA VALIDAÇÃO DOS CUPONS FISCAIS</h5>
                    <p>7.1. Os cupons fiscais cadastrados serão submetidos a processo de validação, podendo apresentar os seguintes status:</p>
                    <ul>
                        <li><strong>Validado:</strong> Cupom fiscal verificado e aprovado. Os números da sorte correspondentes serão gerados automaticamente pelo sistema;</li>
                        <li><strong>Pendente:</strong> Cupom fiscal aguardando verificação e processamento;</li>
                        <li><strong>Rejeitado:</strong> Cupom fiscal que não atendeu aos critérios de validação. Nenhum número da sorte será gerado.</li>
                    </ul>
                    <p>7.2. A combinação de número do cupom fiscal e CNPJ do estabelecimento emitente é única. Não será permitido o cadastro em duplicidade do mesmo cupom fiscal.</p>

                    <h5 class="fw-bold mt-4">8. DOS SORTEIOS</h5>
                    <p>8.1. Os sorteios serão realizados conforme calendário previamente definido e divulgado pela organização.</p>
                    <p>8.2. A apuração dos números sorteados será efetuada com base nos resultados oficiais da Loteria Federal do Brasil, conforme metodologia a ser oportunamente divulgada.</p>

                    <h5 class="fw-bold mt-4">9. DOS PRÊMIOS</h5>
                    <p>9.1. Os prêmios serão definidos, especificados e divulgados pela organização previamente à realização de cada sorteio.</p>
                    <p>9.2. A entrega dos prêmios será realizada conforme condições e prazos estabelecidos pela organização.</p>
                    <p>9.3. Os prêmios são pessoais e intransferíveis, não podendo ser convertidos em dinheiro.</p>

                    <h5 class="fw-bold mt-4">10. DAS DISPOSIÇÕES GERAIS</h5>
                    <p>10.1. A participação na Promoção é gratuita, não sendo exigido qualquer pagamento, contribuição ou contraprestação além da aquisição dos produtos participantes.</p>
                    <p>10.2. A organização reserva-se o direito de desclassificar, a qualquer tempo, participantes que descumpram quaisquer das disposições deste Regulamento, sem prejuízo das medidas legais cabíveis.</p>
                    <p>10.3. A organização não se responsabiliza por falhas técnicas, de comunicação, de acesso à internet ou de qualquer outra natureza que impeçam ou dificultem o cadastro ou a participação na Promoção.</p>
                    <p>10.4. A organização poderá, a seu exclusivo critério e mediante prévia comunicação, alterar, suspender ou cancelar a Promoção, sem que isso gere qualquer direito a indenização aos participantes.</p>
                    <p>10.5. Os casos omissos ou não previstos neste Regulamento serão resolvidos exclusivamente pela organização da Promoção, cujas decisões serão soberanas e irrecorríveis.</p>
                    <p>10.6. Fica eleito o foro da comarca da sede da empresa organizadora para dirimir quaisquer questões oriundas deste Regulamento, com renúncia expressa a qualquer outro, por mais privilegiado que seja.</p>

                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Dúvidas?</strong> Entre em contato pelo e-mail <a href="mailto:contato@promocaomodelo.com.br">contato@promocaomodelo.com.br</a>.
                    </div>

                    <p class="text-muted small mt-3 mb-0 text-center">
                        <i class="bi bi-calendar-check me-1"></i>Regulamento atualizado em {{ date('d/m/Y') }}.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

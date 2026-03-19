<?php
require_once __DIR__ . '/../config/conexao.php';

// carregamento da biblioteca Dompdf
if (file_exists(__DIR__ . '/../libs/dompdf/autoload.inc.php')) {
    require_once __DIR__ . '/../libs/dompdf/autoload.inc.php';
} else {
    die("Erro: A biblioteca Dompdf não foi encontrada na pasta libs.");
}

use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET['id'])) {
    $id_contrato = $_GET['id'];

    // aqui é a consulta correta, 
    // isso significa que ele vai buscar o contrato e o aluno esepcifico desse contrato pra evitar troca de nomes entre irmaos
    $sql = "SELECT c.*, a.*, cl.*, e.nomeEscola 
            FROM tb_contrato c
            INNER JOIN tb_alunos a ON c.id_aluno = a.id_aluno
            INNER JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
            LEFT JOIN tb_escolas e ON a.id_escola = e.id
            WHERE c.id_contrato = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_contrato]);
    $d = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$d) die("Erro: Contrato não encontrado.");

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // formataçao de datas pra cláusula 5
    $dataInicioFormatada = date('d/m/Y', strtotime($d->dataInicioContrato));
    $dataFimFormatada = date('d/m/Y', strtotime($d->dataFinalContrato));
    
    // meses pra data final de assinatura
    $meses = ["01"=>"Janeiro","02"=>"Fevereiro","03"=>"Março","04"=>"Abril","05"=>"Maio","06"=>"Junho","07"=>"Julho","08"=>"Agosto","09"=>"Setembro","10"=>"Outubro","11"=>"Novembro","12"=>"Dezembro"];
    $dataAtualExtenso = date('d') . " de " . $meses[date('m')] . " de " . date('Y');

    $html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.4; color: #000; }
            .header { text-align: center; font-weight: bold; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 5px; }
            .box { border: 1px solid #000; padding: 10px; margin-bottom: 15px; }
            .title { text-align: center; font-size: 12pt; font-weight: bold; margin: 15px 0; text-decoration: underline; }
            .clausula { text-align: justify; margin-bottom: 8px; }
            .footer { margin-top: 50px; }
            .signature { width: 45%; display: inline-block; border-top: 1px solid #000; text-align: center; margin-top: 50px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <div style='font-size: 14pt;'>TRANSPORTE ESCOLAR - TIA MARCIA & TIO NANÁ</div>
            <div>Fones: 2524-8772, 97466-8402 ou 97132-1186</div>
        </div>

        <div class='box'>
            <strong>Aluno(a):</strong> {$d->nomeCompleto} | <strong>Nasc:</strong> " . date('d/m/Y', strtotime($d->dataNascimento)) . "<br>
            <strong>Pai/Mãe:</strong> {$d->NomeCompleto} | <strong>RG:</strong> {$d->rg} | <strong>CPF:</strong> {$d->cpf}<br>
            <strong>Endereço:</strong> {$d->logradouro}, {$d->numero} - {$d->bairro} | <strong>CEP:</strong> {$d->cep}<br>
            <strong>Colégio:</strong> {$d->nomeEscola} | <strong>Série:</strong> {$d->serie}<br>
            <strong>Horário:</strong> {$d->horario_aluno} | <strong>Serviço:</strong> {$d->tipo_transporte}
        </div>

        <div class='title'>CONTRATO PARTICULAR DE PRESTAÇÃO DE SERVIÇO</div>

        <div class='clausula'>1ª) O CONTRATADO compromete-se a transportar o aluno(a) acima identificado da sua residência a escola e vice-versa, em seu período normal de aulas, de conformidade com o cadastro acima transcrito.</div>
        <div class='clausula'>2ª) O aluno (a) deverá estar pronto na porta de sua residência para que não haja atraso no horário estabelecido pela escola. </div>
        <div class='clausula'>3ª) O aluno (a) tem que acatar as ordens do auxiliar ou motorista para o bem estar de todos. </div>
        <div class='clausula'>4ª) O Contratante deverá comunicar a desistência com 30 dias de antecedência. </div>
        
        <div class='clausula'><strong>5ª) O valor total do contrato será de R$ " . number_format($d->valorTotalContrato, 2, ',', '.') . ", que será pago em {$d->qtdParcela} parcelas de R$ " . number_format($d->valorParcela, 2, ',', '.') . " tendo início em {$dataInicioFormatada} e término em {$dataFimFormatada}.</strong> </div>

        <div class='clausula'>6ª) As parcelas não quitadas até o vencimento serão acrescidas de R$ 20,00 de multa mais juros diários de 1%.</div>
        <div class='clausula'>7ª) Para as inscrições dos novos alunos(a)s, será cobrada a matrícula assegurando a vaga.</div>
        <div class='clausula'>8ª) As parcelas serão corrigidas toda vez que houver aumento do combustível.</div>
        <div class='clausula'>9º) O período de férias será cobrado normalmente, devido ser um contrato com valor fechado.</div>
        <div class='clausula'>10º) Se o responsável cancelar o transporte em qualquer época do ano deverá pagar multa referente a duas parcelas do valor do contrato. </div>
        <div class='clausula'>11ª) O CONTRATADO compromete-se a oferecer veículos em bom estado e condições de uso.</div>
        <div class='clausula'>12ª) Não será permitido comer no interior do veículo. </div>
        <div class='clausula'>13ª) Os pais ficam responsáveis em avisar com antecedência caso o aluno esteja impossibilitado de ir. </div>
        <div class='clausula'>14ª) Caso o aluno se afaste temporariamente por qualquer motivo esse período será cobrado normalmente. </div>
        <div class='clausula'>15ª) Não transportamos criança doente em hipótese alguma.</div>
        <div class='clausula'>16ª) Em hipótese nenhuma entregamos em outro endereço que não seja o do contrato.</div>
        <div class='clausula'>17ª) Vistorias obrigatórias ocorrerão em Abril e Outubro; nestes dias não haverá serviço. </div>
        <div class='clausula'>18ª) Encerramento das atividades segue o calendário das escolas estaduais (dezembro). </div>
        <div class='clausula'>19ª) Em caso de defeito no veículo, o contratado poderá suspender o transporte por até 3 dias úteis. </div>
        <div class='clausula'>20ª) Em caso de passeios ou reuniões fora do horário, o transporte não será prestado. </div>

        <p style='text-align: right; margin-top: 30px;'>São Paulo, {$dataAtualExtenso}.</p>

        <div class='footer'>
            <div class='signature'>TIA MARCIA & TIO NANÁ<br><small>Contratado</small> </div>
            <div class='signature' style='float:right;'>{$d->NomeCompleto}<br><small>Contratante</small> </div>
        </div>
    </body>
    </html>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Contrato_{$d->nomeCompleto}.pdf", ["Attachment" => false]);
}
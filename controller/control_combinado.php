<?php
require_once __DIR__ . '/../config/conexao.php'; // IP 10.91.45.51

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction(); 

        // 1. SALVAR CLIENTE
        $sqlCli = "INSERT INTO tb_clientes (NomeCompleto, cpf, rg, telefone, email, logradouro, numero, complemento, bairro, cep, nome_segundo_resp, telefone_segundo_resp, email_segundo_resp) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtCli = $pdo->prepare($sqlCli);
        $stmtCli->execute([
            $_POST['NomeCompleto'], $_POST['cpf'], $_POST['rg'], $_POST['telefone'], $_POST['email'], 
            $_POST['logradouro'], $_POST['numero'], $_POST['complemento'], $_POST['bairro'], $_POST['cep'],
            $_POST['nome_segundo_resp'], $_POST['telefone_segundo_resp'], $_POST['email_segundo_resp']
        ]);
        $idCliente = $pdo->lastInsertId(); 

        // 2. SALVAR ALUNO
        $sqlAlu = "INSERT INTO tb_alunos (nomeCompleto, id_cliente, id_escola, serie, sala, dataNascimento, tipo_transporte, horario_aluno) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtAlu = $pdo->prepare($sqlAlu);
        $stmtAlu->execute([
            $_POST['nomeAluno'], $idCliente, $_POST['id_escola'], $_POST['serie'], $_POST['sala'], 
            $_POST['dataNascimento'], $_POST['tipo_transporte'], $_POST['horario_aluno']
        ]);

        // 3. SALVAR CONTRATO
        $valorTotal = (float)$_POST['valorTotalContrato'];
        $qtdParcela = (int)$_POST['qtdParcela'];
        $valorParcela = $valorTotal / $qtdParcela;

        $sqlCon = "INSERT INTO tb_contrato (id_cliente, valorParcela, qtdParcela, valorTotalContrato, dataInicioContrato, dataFinalContrato, status_contrato) 
                   VALUES (?, ?, ?, ?, ?, ?, 'Ativo')";
        $stmtCon = $pdo->prepare($sqlCon);
        $stmtCon->execute([
            $idCliente, $valorParcela, $qtdParcela, $valorTotal, 
            $_POST['dataInicioContrato'], $_POST['dataFinalContrato']
        ]);
        
        $idContrato = $pdo->lastInsertId(); // Pega o ID do contrato gerado

        // 4. GERAR PARCELAS NO CALENDÁRIO (Loop Automático)
        for ($i = 1; $i <= $qtdParcela; $i++) {
            // Calcula a data de vencimento: soma 1 mês para cada parcela
            $dataVencimento = date('Y-m-d', strtotime($_POST['dataInicioContrato'] . " + " . ($i-1) . " month"));
            
            // O INSERT inclui a data_pagamento na tabela tb_calendario
            $sqlParcela = "INSERT INTO tb_calendario (id_contrato, data_pagamento, confirmacao_pagamento) 
                           VALUES (?, ?, 'pendente')";
            
            $stmtParcela = $pdo->prepare($sqlParcela);
            $stmtParcela->execute([$idContrato, $dataVencimento]);
        }

        $pdo->commit(); 
        header("Location: ../index.php?sucesso=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack(); 
        die("Erro no cadastro unificado: " . $e->getMessage());
    }
}
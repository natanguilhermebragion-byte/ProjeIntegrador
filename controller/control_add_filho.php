<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. SALVAR ALUNO
        $id_escola = !empty($_POST['id_escola']) ? $_POST['id_escola'] : null;
        
        $sqlAlu = "INSERT INTO tb_alunos (nomeCompleto, id_cliente, id_escola, serie, sala, dataNascimento, tipo_transporte, horario_aluno) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtAlu = $pdo->prepare($sqlAlu);
        $stmtAlu->execute([
            $_POST['nomeAluno'], $_POST['id_cliente'], $id_escola, $_POST['serie'], 
            $_POST['sala'], $_POST['dataNascimento'], $_POST['tipo_transporte'], $_POST['horario_aluno']
        ]);

        // 2. SALVAR CONTRATO
        $valorTotal = (float)$_POST['valorTotalContrato'];
        $qtdParcela = (int)$_POST['qtdParcela'];
        $valorParcela = $valorTotal / $qtdParcela;

        $sqlCon = "INSERT INTO tb_contrato (id_cliente, valorParcela, qtdParcela, valorTotalContrato, dataInicioContrato, dataFinalContrato, status_contrato) 
                   VALUES (?, ?, ?, ?, ?, ?, 'Ativo')";
        $stmtCon = $pdo->prepare($sqlCon);
        $stmtCon->execute([
            $_POST['id_cliente'], $valorParcela, $qtdParcela, $valorTotal, 
            $_POST['dataInicioContrato'], $_POST['dataFinalContrato']
        ]);
        
        $idContrato = $pdo->lastInsertId();

        // 3. GERAR CALENDÁRIO COM VENCIMENTO FIXO
        $dataBase = new DateTime($_POST['dataInicioContrato']);
        $diaVenc = (int)$_POST['diaVencimento'];

        for ($i = 0; $i < $qtdParcela; $i++) {
            $dataParc = clone $dataBase;
            $dataParc->modify("+$i month");
            $dataParc->setDate((int)$dataParc->format('Y'), (int)$dataParc->format('m'), $diaVenc);
            
            $sqlP = "INSERT INTO tb_calendario (id_contrato, numero_parcela, data_pagamento, confirmacao_pagamento) VALUES (?, ?, ?, 'pendente')";
            $pdo->prepare($sqlP)->execute([$idContrato, ($i + 1), $dataParc->format('Y-m-d')]);
        }

        $pdo->commit();
        header("Location: ../index.php?sucesso=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao processar: " . $e->getMessage());
    }
}
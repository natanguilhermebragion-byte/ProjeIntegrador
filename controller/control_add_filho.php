<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. SALVAR ALUNO
        // Tratamento para garantir que id_escola seja numérico ou NULL
        $id_escola = !empty($_POST['id_escola']) ? (int)$_POST['id_escola'] : null;
        
        $sqlAlu = "INSERT INTO tb_alunos (nomeCompleto, id_cliente, id_escola, serie, sala, dataNascimento, tipo_transporte, horario_aluno) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtAlu = $pdo->prepare($sqlAlu);
        $stmtAlu->execute([
            $_POST['nomeAluno'], 
            $_POST['id_cliente'], 
            $id_escola, 
            $_POST['serie'], 
            $_POST['sala'], 
            $_POST['dataNascimento'], 
            $_POST['tipo_transporte'], 
            $_POST['horario_aluno']
        ]);

        // 2. PEGA O ID DO ALUNO QUE ACABOU DE SER CRIADO
        $id_novo_aluno = $pdo->lastInsertId();

        // 3. SALVAR CONTRATO VINCULADO AO ALUNO
        $valorTotal = (float)$_POST['valorTotalContrato'];
        $qtdParcela = (int)$_POST['qtdParcela'];
        $valorParcela = $valorTotal / $qtdParcela;

        $sqlCon = "INSERT INTO tb_contrato (id_cliente, id_aluno, valorParcela, qtdParcela, valorTotalContrato, dataInicioContrato, dataFinalContrato, status_contrato) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, 'Ativo')";
        $stmtCon = $pdo->prepare($sqlCon);
        $stmtCon->execute([
            $_POST['id_cliente'], 
            $id_novo_aluno, 
            $valorParcela, 
            $qtdParcela, 
            $valorTotal, 
            $_POST['dataInicioContrato'], 
            $_POST['dataFinalContrato']
        ]);
        
        $idContrato = $pdo->lastInsertId();

        // 4. GERAR CALENDÁRIO COM VENCIMENTO FIXO
        $dataBase = new DateTime($_POST['dataInicioContrato']);
        $diaVenc = (int)$_POST['diaVencimento'];

        for ($i = 0; $i < $qtdParcela; $i++) {
            $dataParc = clone $dataBase;
            $dataParc->modify("+$i month");
            
            $ano = (int)$dataParc->format('Y');
            $mes = (int)$dataParc->format('m');
            $dataParc->setDate($ano, $mes, $diaVenc);
            
            $sqlP = "INSERT INTO tb_calendario (id_contrato, numero_parcela, data_pagamento, confirmacao_pagamento) 
                     VALUES (?, ?, ?, 'pendente')";
            $pdo->prepare($sqlP)->execute([
                $idContrato, 
                ($i + 1), 
                $dataParc->format('Y-m-d')
            ]);
        }

        // Finaliza a transação com sucesso
        $pdo->commit();
        
        // --- AUTOMAÇÃO PDF ---
        // Redireciona para a tela intermediária que oferece o botão de gerar PDF
        header("Location: ../view/sucesso_cadastro.php?id_contrato=" . $idContrato);
        exit;

    } catch (Exception $e) {
        // Se algo der errado, desfaz tudo para não ter lixo no banco
        if ($pdo->inTransaction()) { 
            $pdo->rollBack(); 
        }
        die("Erro ao processar cadastro de filho e contrato: " . $e->getMessage());
    }
}
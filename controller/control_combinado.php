<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. INSERIR CLIENTE (RESPONSÁVEL)
        $sqlCli = "INSERT INTO tb_clientes (NomeCompleto, cpf, rg, telefone, email, cep, logradouro, numero, bairro, complemento, nome_segundo_resp, telefone_segundo_resp, email_segundo_resp) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtCli = $pdo->prepare($sqlCli);
        $stmtCli->execute([
            $_POST['NomeCompleto'], $_POST['cpf'], $_POST['rg'], $_POST['telefone'], 
            $_POST['email'], $_POST['cep'], $_POST['logradouro'], $_POST['numero'], 
            $_POST['bairro'], $_POST['complemento'], $_POST['nome_segundo_resp'], 
            $_POST['telefone_segundo_resp'], $_POST['email_segundo_resp']
        ]);
        $id_cliente = $pdo->lastInsertId();

        // 2. INSERIR ALUNO (VINCULADO AO CLIENTE)
        $id_escola = !empty($_POST['id_escola']) ? (int)$_POST['id_escola'] : null;
        $sqlAlu = "INSERT INTO tb_alunos (nomeCompleto, id_cliente, id_escola, serie, sala, dataNascimento, tipo_transporte, horario_aluno) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtAlu = $pdo->prepare($sqlAlu);
        $stmtAlu->execute([
            $_POST['nomeAluno'], $id_cliente, $id_escola, $_POST['serie'], 
            $_POST['sala'], $_POST['dataNascimento'], $_POST['tipo_transporte'], $_POST['horario_aluno']
        ]);
        
        // PEGAMOS O ID DO ALUNO RECENTE PARA O VÍNCULO DO CONTRATO
        $id_aluno = $pdo->lastInsertId();

        // 3. INSERIR CONTRATO (VINCULADO AO CLIENTE E AO ALUNO)
        $valorTotal = (float)$_POST['valorTotalContrato'];
        $qtdParcela = (int)$_POST['qtdParcela'];
        $valorParcela = $valorTotal / $qtdParcela;

        $sqlCon = "INSERT INTO tb_contrato (id_cliente, id_aluno, valorParcela, qtdParcela, valorTotalContrato, dataInicioContrato, dataFinalContrato, status_contrato) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, 'Ativo')";
        $stmtCon = $pdo->prepare($sqlCon);
        $stmtCon->execute([
            $id_cliente, 
            $id_aluno, 
            $valorParcela, $qtdParcela, $valorTotal, 
            $_POST['dataInicioContrato'], $_POST['dataFinalContrato']
        ]);
        $id_contrato = $pdo->lastInsertId();

        // 4. GERAR CALENDÁRIO COM VENCIMENTO FIXO
        $dataBase = new DateTime($_POST['dataInicioContrato']);
        $diaVenc = (int)$_POST['diaVencimento'];

        for ($i = 0; $i < $qtdParcela; $i++) {
            $dataParc = clone $dataBase;
            $dataParc->modify("+$i month");
            
            // Força o dia da parcela para o dia de vencimento escolhido
            $dataParc->setDate((int)$dataParc->format('Y'), (int)$dataParc->format('m'), $diaVenc);
            
            $sqlP = "INSERT INTO tb_calendario (id_contrato, numero_parcela, data_pagamento, confirmacao_pagamento) 
                     VALUES (?, ?, ?, 'pendente')";
            $pdo->prepare($sqlP)->execute([$id_contrato, ($i + 1), $dataParc->format('Y-m-d')]);
        }

        // Finaliza a transação com sucesso
        $pdo->commit();

        // --- REDIRECIONAMENTO PARA TELA DE PDF ---
        header("Location: ../view/sucesso_cadastro.php?id_contrato=" . $id_contrato);
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Erro no cadastro unificado: " . $e->getMessage());
    }
}
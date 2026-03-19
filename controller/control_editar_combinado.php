<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        
        $id_contrato = $_POST['id_contrato'];
        $id_cliente  = $_POST['id_cliente'];
        $novoValorTotal = (float)$_POST['valorTotalContrato'];
        $novaQtdParcelas = (int)$_POST['qtdParcela'];
        $novoValorParcela = ($novaQtdParcelas > 0) ? ($novoValorTotal / $novaQtdParcelas) : 0;

        // Atualiza cliente e aluno (completo)
        $sqlCli = "UPDATE tb_clientes SET 
                    NomeCompleto = ?, cpf = ?, rg = ?, telefone = ?, email = ?, 
                    logradouro = ?, numero = ?, bairro = ?, cep = ? 
                   WHERE id_cliente = ?";
        $pdo->prepare($sqlCli)->execute([
            $_POST['NomeCompleto'], $_POST['cpf'], $_POST['rg'], $_POST['telefone'], $_POST['email'], 
            $_POST['logradouro'], $_POST['numero'], $_POST['bairro'], $_POST['cep'], $id_cliente
        ]);

        $sqlAlu = "UPDATE tb_alunos SET 
                    nomeCompleto = ?, id_escola = ?, serie = ? 
                   WHERE id_cliente = ?";
        $pdo->prepare($sqlAlu)->execute([
            $_POST['nomeAluno'], $_POST['id_escola'], $_POST['serie'], $id_cliente
        ]);

        // busca dados antigos do contrato pra comparar a quantidade de parcelas
        $stmtAntigo = $pdo->prepare("SELECT qtdParcela, dataInicioContrato FROM tb_contrato WHERE id_contrato = ?");
        $stmtAntigo->execute([$id_contrato]);
        $contratoAntigo = $stmtAntigo->fetch(PDO::FETCH_OBJ);

        // atualiza o contrato com novos valores, valor da parcela e quantidade
        $sqlCon = "UPDATE tb_contrato SET 
                    valorTotalContrato = ?, valorParcela = ?, qtdParcela = ?, status_contrato = ? 
                   WHERE id_contrato = ?";
        $pdo->prepare($sqlCon)->execute([
            $novoValorTotal, $novoValorParcela, $novaQtdParcelas, $_POST['status_contrato'], $id_contrato
        ]);

        // Recalcular parcelas
        if ($contratoAntigo->qtdParcela != $novaQtdParcelas) {

            // se quantidade de parcelas foi alterada:
            
            
            $pdo->prepare("DELETE FROM tb_calendario WHERE id_contrato = ? AND confirmacao_pagamento = 'pendente'")->execute([$id_contrato]);

            
            $stmtPagas = $pdo->prepare("SELECT COUNT(*) as total FROM tb_calendario WHERE id_contrato = ? AND confirmacao_pagamento = 'pago'");
            $stmtPagas->execute([$id_contrato]);
            $totalPagas = $stmtPagas->fetch(PDO::FETCH_OBJ)->total;

           
            for ($i = $totalPagas + 1; $i <= $novaQtdParcelas; $i++) {

            
                $dataVencimento = date('Y-m-d', strtotime($contratoAntigo->dataInicioContrato . " + " . ($i-1) . " month"));
                
                $sqlNovoCalendario = "INSERT INTO tb_calendario (id_contrato, numero_parcela, data_pagamento, confirmacao_pagamento) VALUES (?, ?, ?, 'pendente')";
                $pdo->prepare($sqlNovoCalendario)->execute([$id_contrato, $i, $dataVencimento]);
            }
        } else {
            // se a quantidade nao mudou:
            if (isset($_POST['parcelas']) && is_array($_POST['parcelas'])) {
                foreach ($_POST['parcelas'] as $id_cal => $dados_p) {
                    $sqlUpParcela = "UPDATE tb_calendario SET data_pagamento = ?, confirmacao_pagamento = ? WHERE id_calendario = ?";
                    $pdo->prepare($sqlUpParcela)->execute([$dados_p['data'], $dados_p['status'], $id_cal]);
                }
            }
        }

        $pdo->commit();
        header("Location: ../index.php?editado=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao atualizar contrato: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    exit;
}
<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_contrato = $_POST['id_contrato'];
    $nova_qtd = (int)$_POST['qtdParcela'];
    $valor_total = (float)$_POST['valorTotalContrato'];
    $novo_valor_parcela = $valor_total / $nova_qtd;

    try {
        $pdo->beginTransaction();

        // 1. Busca a quantidade antiga para comparar
        $stmtCheck = $pdo->prepare("SELECT qtdParcela FROM tb_contrato WHERE id_contrato = ?");
        $stmtCheck->execute([$id_contrato]);
        $qtd_antiga = $stmtCheck->fetchColumn();

        // 2. Atualiza os dados do contrato
        $sqlCon = "UPDATE tb_contrato SET 
                   valorTotalContrato = ?, 
                   qtdParcela = ?,
                   valorParcela = ?,
                   dataInicioContrato = ?, 
                   dataFinalContrato = ?, 
                   status_contrato = ? 
                   WHERE id_contrato = ?";
        $pdo->prepare($sqlCon)->execute([
            $valor_total, $nova_qtd, $novo_valor_parcela,
            $_POST['dataInicioContrato'], $_POST['dataFinalContrato'],
            $_POST['status_contrato'], $id_contrato
        ]);

        // 3. Se a quantidade de parcelas mudou, regeramos o calendário
        if ($nova_qtd != $qtd_antiga) {
            // Apaga parcelas atuais (Cuidado: isso remove histórico de pagamentos confirmados deste contrato)
            $pdo->prepare("DELETE FROM tb_calendario WHERE id_contrato = ?")->execute([$id_contrato]);

            // Gera novas parcelas
            $dataBase = new DateTime($_POST['dataInicioContrato']);
            $diaVenc = (int)$_POST['diaVencimento'];

            for ($i = 0; $i < $nova_qtd; $i++) {
                $dataParc = clone $dataBase;
                $dataParc->modify("+$i month");
                $dataParc->setDate((int)$dataParc->format('Y'), (int)$dataParc->format('m'), $diaVenc);
                
                $sqlP = "INSERT INTO tb_calendario (id_contrato, numero_parcela, data_pagamento, confirmacao_pagamento) VALUES (?, ?, ?, 'pendente')";
                $pdo->prepare($sqlP)->execute([$id_contrato, ($i + 1), $dataParc->format('Y-m-d')]);
            }
        } else {
            // Se a quantidade não mudou, apenas atualiza as datas das parcelas existentes se foram alteradas manualmente
            if (!empty($_POST['parcelas'])) {
                $stmtParc = $pdo->prepare("UPDATE tb_calendario SET data_pagamento = ? WHERE id_calendario = ?");
                foreach ($_POST['parcelas'] as $p) {
                    $stmtParc->execute([$p['data'], $p['id']]);
                }
            }
        }

        $pdo->commit();
        header("Location: ../index.php?msg=contrato_atualizado");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao atualizar contrato e parcelas: " . $e->getMessage());
    }
}
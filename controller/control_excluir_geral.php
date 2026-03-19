<?php
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id_contrato'])) {
    $id_contrato = $_GET['id_contrato'];

    try {
        $pdo->beginTransaction();

        // 1. Apagar as parcelas do calendário ligadas a este contrato (Evita o erro 1451)
        $sql1 = "DELETE FROM tb_calendario WHERE id_contrato = ?";
        $pdo->prepare($sql1)->execute([$id_contrato]);

        // 2. Apagar o contrato
        $sql2 = "DELETE FROM tb_contrato WHERE id_contrato = ?";
        $pdo->prepare($sql2)->execute([$id_contrato]);

        // NOTA: Se você quiser apagar apenas o contrato e manter o cliente vivo, 
        // a operação deve parar aqui.

        $pdo->commit();
        header("Location: ../index.php?excluido=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao excluir contrato: " . $e->getMessage());
    }
}
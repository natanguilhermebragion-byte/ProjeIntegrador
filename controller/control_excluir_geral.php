<?php
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id_contrato'])) {
    $id_contrato = $_GET['id_contrato'];

    try {
        $pdo->beginTransaction();

        // apagar as parcelas do calendário ligadas a este contrato
        $sql1 = "DELETE FROM tb_calendario WHERE id_contrato = ?";
        $pdo->prepare($sql1)->execute([$id_contrato]);

        // apagar o contrato
        $sql2 = "DELETE FROM tb_contrato WHERE id_contrato = ?";
        $pdo->prepare($sql2)->execute([$id_contrato]);

       

        $pdo->commit();
        header("Location: ../index.php?excluido=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao excluir contrato: " . $e->getMessage());
    }
}
<?php
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id_contrato']) && isset($_GET['id_cliente'])) {
    try {
        $pdo->beginTransaction();

        // 1. Excluir parcelas do calendário associadas ao contrato
        $stmt = $pdo->prepare("DELETE FROM tb_calendario WHERE id_contrato = ?");
        $stmt->execute([$_GET['id_contrato']]);

        // 2. Excluir o contrato
        $stmt = $pdo->prepare("DELETE FROM tb_contrato WHERE id_contrato = ?");
        $stmt->execute([$_GET['id_contrato']]);

        // 3. Excluir os alunos vinculados ao cliente
        $stmt = $pdo->prepare("DELETE FROM tb_alunos WHERE id_cliente = ?");
        $stmt->execute([$_GET['id_cliente']]);

        // 4. Excluir o cliente/responsável
        $stmt = $pdo->prepare("DELETE FROM tb_clientes WHERE id_cliente = ?");
        $stmt->execute([$_GET['id_cliente']]);

        $pdo->commit();
        header("Location: ../index.php?excluido=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro crítico ao excluir: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    exit;
}
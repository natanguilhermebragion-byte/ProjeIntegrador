<?php
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status']; // 'Ativo', 'Finalizado' ou 'Pendente'

    try {
        // Atualiza o status manualmente na tabela de contrato
        $sql = "UPDATE tb_contrato SET status_contrato = :status WHERE id_contrato = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':status' => $status, ':id' => $id]);

        header("Location: ../index.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar status do contrato: " . $e->getMessage());
    }
}
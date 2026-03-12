<?php
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Atualiza o status na tb_calendario para 'confirmado'
        $sql = "UPDATE tb_calendario SET confirmacao_pagamento = 'confirmado' WHERE id_calendario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Volta para o painel principal
        header("Location: ../index.php");
        exit;

    } catch (PDOException $e) {
        die("Erro ao atualizar pagamento: " . $e->getMessage());
    }
}
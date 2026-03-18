<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id'])) {
    $id_calendario = $_GET['id'];

    try {
        // Altera o status de volta para pendente
        $sql = "UPDATE tb_calendario SET confirmacao_pagamento = 'pendente' WHERE id_calendario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_calendario]);

        // Redireciona com um aviso de estorno realizado
        header("Location: ../index.php?estorno=1");
        exit;

    } catch (PDOException $e) {
        die("Erro ao processar estorno: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    exit;
}
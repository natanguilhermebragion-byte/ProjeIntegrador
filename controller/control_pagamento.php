<?php
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Atualiza o status para 'confirmado' na tb_calendario
        $sql = "UPDATE tb_calendario SET confirmacao_pagamento = 'confirmado' WHERE id_calendario = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            // Volta para o painel com um marcador de sucesso na URL
            header("Location: ../index.php?sucesso_pagamento=1");
            exit;
        } else {
            echo "Erro ao processar o pagamento no banco de dados.";
        }

    } catch (PDOException $e) {
        die("Erro crítico ao atualizar pagamento: " . $e->getMessage());
    }
} else {
    // Se acessar o arquivo sem ID, volta para o index
    header("Location: ../index.php");
    exit;
}
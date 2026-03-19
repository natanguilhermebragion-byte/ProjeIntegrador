<?php

require_once __DIR__ . '/../config/conexao.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nome     = $_POST['nome'] ?? '';
    $cpf      = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email    = $_POST['email'] ?? '';

    try {
       
        $sql = "INSERT INTO tb_usuario (nome, cpf, telefone, email) VALUES (:nome, :cpf, :telefone, :email)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nome'     => $nome,
            ':cpf'      => $cpf,
            ':telefone' => $telefone,
            ':email'    => $email
        ]);

      
        header("Location: ../view/cad_usuario.php?sucesso=1");
        exit;

    } catch (PDOException $e) {
        // mostraa o erro caso a conexão falhe
        die("Erro ao cadastrar: " . $e->getMessage());
    }
}
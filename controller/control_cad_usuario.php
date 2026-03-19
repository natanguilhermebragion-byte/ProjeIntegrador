<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    
    // criptografia da senha :3
    $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO tb_usuario (nome, login, senha, cpf, telefone, email) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $login, $senhaHash, $cpf, $telefone, $email]);

        header("Location: ../index.php?sucesso_user=1");
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            die("Erro: Este Login ou E-mail já está em uso.");
        }
        die("Erro ao cadastrar: " . $e->getMessage());
    }
}
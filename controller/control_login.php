<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    // Busca o usuário pelo login
    $sql = "SELECT * FROM tb_usuario WHERE login = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    // Verifica se usuário existe e se a senha bate com o hash no banco
    if ($user && password_verify($senha, $user->senha)) {
        // Cria a sessão de login
        $_SESSION['usuario_id'] = $user->id_usuario;
        $_SESSION['usuario_nome'] = $user->nome;
        
        header("Location: ../index.php");
        exit;
    } else {
        // Falha no login
        header("Location: ../login.php?erro=1");
        exit;
    }
}
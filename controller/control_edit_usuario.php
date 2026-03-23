<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id_usuario'];
    $nome     = $_POST['nome'];
    $login    = $_POST['login'];
    $email    = $_POST['email']; // Agora capturando corretamente
    $cpf      = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $nova_senha = $_POST['senha']; // Alinhado com o nome do campo no seu formulário

    try {
        if (!empty($nova_senha)) {
            // Atualiza todos os dados, incluindo a nova senha criptografada
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql = "UPDATE tb_usuario SET 
                        nome = ?, 
                        login = ?, 
                        email = ?, 
                        cpf = ?, 
                        telefone = ?, 
                        senha = ? 
                    WHERE id_usuario = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $login, $email, $cpf, $telefone, $hash, $id]);
        } else {
            // Atualiza apenas os dados básicos, mantendo a senha atual
            $sql = "UPDATE tb_usuario SET 
                        nome = ?, 
                        login = ?, 
                        email = ?, 
                        cpf = ?, 
                        telefone = ? 
                    WHERE id_usuario = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $login, $email, $cpf, $telefone, $id]);
        }

        // Redireciona com mensagem de sucesso
        header("Location: ../index.php?msg=usuario_atualizado");
        exit;

    } catch (Exception $e) {
        die("Erro ao atualizar usuário: " . $e->getMessage());
    }
}
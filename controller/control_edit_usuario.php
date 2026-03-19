<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_usuario'];
    $nome = $_POST['nome'];
    $login = $_POST['login'];
    $nova_senha = $_POST['nova_senha'];

    try {
        if (!empty($nova_senha)) {

            // atualiza com nova senha criptografada
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql = "UPDATE tb_usuario SET nome = ?, login = ?, senha = ? WHERE id_usuario = ?";
            $pdo->prepare($sql)->execute([$nome, $login, $hash, $id]);
        } else {

            // atualiza apenas dados basicos
            $sql = "UPDATE tb_usuario SET nome = ?, login = ? WHERE id_usuario = ?";
            $pdo->prepare($sql)->execute([$nome, $login, $id]);
        }
        header("Location: ../index.php?user_edit=1");
    } catch (Exception $e) {
        die("Erro ao atualizar usuário: " . $e->getMessage());
    }
}
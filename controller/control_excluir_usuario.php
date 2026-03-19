<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id'])) {
    $id_para_excluir = $_GET['id'];

    // impede que o usuario logado apague a si mesmo por acidente
    if ($id_para_excluir == $_SESSION['usuario_id']) {
        die("Erro: Você não pode excluir sua própria conta enquanto estiver logado.");
    }

    $stmt = $pdo->prepare("DELETE FROM tb_usuario WHERE id_usuario = ?");
    $stmt->execute([$id_para_excluir]);

    header("Location: ../index.php?user_del=1");
    exit;
}
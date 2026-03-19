<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id'])) {
    $id_aluno = $_GET['id'];

    try {
        // remove só o aluno da tb_alunos
        $stmt = $pdo->prepare("DELETE FROM tb_alunos WHERE id_aluno = ?");
        $stmt->execute([$id_aluno]);

        // redireciona de volta pro painel na aba de alunos
        header("Location: ../index.php?aluno_removido=1");
        exit;

    } catch (PDOException $e) {
        die("Erro ao remover aluno: " . $e->getMessage());
    }
}
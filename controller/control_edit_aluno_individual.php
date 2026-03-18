<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aluno = $_POST['id_aluno'];
    $nome = $_POST['nomeCompleto'];
    $nascimento = $_POST['dataNascimento'];
    $serie = $_POST['serie'];
    $sala = $_POST['sala'];
    $horario = $_POST['horario_aluno'];
    $escola = $_POST['id_escola'];
    $transporte = $_POST['tipo_transporte'];

    try {
        $sql = "UPDATE tb_alunos SET 
                nomeCompleto = ?, 
                dataNascimento = ?, 
                serie = ?, 
                sala = ?, 
                horario_aluno = ?, 
                id_escola = ?, 
                tipo_transporte = ? 
                WHERE id_aluno = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $nascimento, $serie, $sala, $horario, $escola, $transporte, $id_aluno]);

        header("Location: ../index.php?aluno_editado=1");
        exit;

    } catch (PDOException $e) {
        die("Erro ao atualizar aluno: " . $e->getMessage());
    }
}
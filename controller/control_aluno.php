<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeCompleto    = $_POST['nomeCompleto'] ?? '';
    $serie           = $_POST['serie'] ?? '';
    $sala            = $_POST['sala'] ?? '';
    $id_cliente      = $_POST['id_cliente'] ?? '';
    $id_escola       = $_POST['id_escola'] ?? '';
    $periodoDeEstudo = $_POST['periodoDeEstudo'] ?? '';

    try {
        // SQL baseado na estrutura da tb_alunos
        $sql = "INSERT INTO tb_alunos (nomeCompleto, serie, sala, id_cliente, id_escola, periodoDeEstudo) 
                VALUES (:nome, :serie, :sala, :id_cliente, :id_escola, :periodo)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'       => $nomeCompleto,
            ':serie'      => $serie,
            ':sala'       => $sala,
            ':id_cliente' => $id_cliente,
            ':id_escola'  => $id_escola,
            ':periodo'    => $periodoDeEstudo
        ]);

        header("Location: ../view/cad_aluno.php?sucesso=1");
        exit;

    } catch (PDOException $e) {
        die("Erro ao cadastrar aluno: " . $e->getMessage());
    }
}
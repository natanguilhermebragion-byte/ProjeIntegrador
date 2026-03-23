<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeEscola     = $_POST['nomeEscola'];
    $enderecoEscola = $_POST['enderecoEscola'];
    $bairro         = $_POST['bairro'];
    $cep            = $_POST['cep'];

    try {
        // Query atualizada com os campos da sua imagem
        $sql = "INSERT INTO tb_escolas (nomeEscola, endereçoEscola, bairro, cep) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomeEscola, $enderecoEscola, $bairro, $cep]);

        // Redireciona de volta para o painel
        header("Location: ../index.php?msg=escola_cadastrada");
        exit;
    } catch (PDOException $e) {
        // Caso dê erro, exibe a mensagem amigável
        die("Erro ao cadastrar escola no banco de dados: " . $e->getMessage());
    }
}
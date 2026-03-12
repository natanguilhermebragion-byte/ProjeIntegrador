<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = $_POST['NomeCompleto'] ?? '';
    $cpf      = $_POST['cpf'] ?? '';
    $email    = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $rg       = $_POST['rg'] ?? '';
    $cep      = $_POST['cep'] ?? ''; // Adicione esta linha

    try {
        // Adicione o campo 'cep' no INSERT e no VALUES
        $sql = "INSERT INTO tb_clientes (NomeCompleto, cpf, email, telefone, endereco, rg, cep) 
                VALUES (:nome, :cpf, :email, :telefone, :endereco, :rg, :cep)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nome'     => $nome,
            ':cpf'      => $cpf,
            ':email'    => $email,
            ':telefone' => $telefone,
            ':endereco' => $endereco,
            ':rg'       => $rg,
            ':cep'      => $cep // Envie o valor aqui
        ]);

        header("Location: ../view/cad_cliente.php?sucesso=1");
        exit;

    } catch (PDOException $e) {
        die("Erro ao cadastrar cliente: " . $e->getMessage());
    }
}
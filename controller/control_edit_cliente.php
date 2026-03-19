<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_cliente'];
    
    $sql = "UPDATE tb_clientes SET 
            NomeCompleto = ?, cpf = ?, rg = ?, email = ?, 
            telefone = ?, logradouro = ?, numero = ?, bairro = ? 
            WHERE id_cliente = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['NomeCompleto'], $_POST['cpf'], $_POST['rg'], $_POST['email'],
            $_POST['telefone'], $_POST['logradouro'], $_POST['numero'], $_POST['bairro'],
            $id
        ]);

        header("Location: ../index.php?msg=cliente_editado");
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar: " . $e->getMessage());
    }
}
<?php
// 1. Ajuste para encontrar a conexão saindo da pasta controller
require_once __DIR__ . '/../config/conexao.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Captura os dados do formulário
    $nome     = $_POST['nome'] ?? '';
    $cpf      = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email    = $_POST['email'] ?? '';

    try {
        // 3. Prepara o SQL de inserção baseado na sua tb_usuario
        $sql = "INSERT INTO tb_usuario (nome, cpf, telefone, email) VALUES (:nome, :cpf, :telefone, :email)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nome'     => $nome,
            ':cpf'      => $cpf,
            ':telefone' => $telefone,
            ':email'    => $email
        ]);

        // 4. Redirecionamento corrigido para voltar à pasta view
        header("Location: ../view/cad_usuario.php?sucesso=1");
        exit;

    } catch (PDOException $e) {
        // Exibe o erro caso a conexão falhe
        die("Erro ao cadastrar: " . $e->getMessage());
    }
}
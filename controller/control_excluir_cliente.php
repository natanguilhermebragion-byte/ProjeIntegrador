<?php
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id'])) {
    $id_cliente = (int)$_GET['id'];

    try {
        $pdo->beginTransaction();

        // 1. Buscar contratos do cliente para limpar as parcelas antes
        $contratos = $pdo->prepare("SELECT id_contrato FROM tb_contrato WHERE id_cliente = ?");
        $contratos->execute([$id_cliente]);
        $ids = $contratos->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($ids)) {
            $inQuery = implode(',', array_fill(0, count($ids), '?'));
            // Apagar todas as parcelas desses contratos
            $pdo->prepare("DELETE FROM tb_calendario WHERE id_contrato IN ($inQuery)")->execute($ids);
            // Apagar os contratos
            $pdo->prepare("DELETE FROM tb_contrato WHERE id_cliente = ?")->execute([$id_cliente]);
        }

        // 2. Apagar os alunos vinculados
        $pdo->prepare("DELETE FROM tb_alunos WHERE id_cliente = ?")->execute([$id_cliente]);

        // 3. Por fim, apagar o cliente
        $pdo->prepare("DELETE FROM tb_clientes WHERE id_cliente = ?")->execute([$id_cliente]);

        $pdo->commit();
        header("Location: ../index.php?msg=cliente_excluido");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao excluir cliente e dependências: " . $e->getMessage());
    }
}
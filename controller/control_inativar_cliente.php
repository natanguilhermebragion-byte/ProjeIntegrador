<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../index.php?msg=erro_id");
    exit;
}

$id_cliente = $_GET['id'];

try {
    // Iniciamos uma transação para garantir a integridade em cascata
    $pdo->beginTransaction();

    // 1. Inativar o Responsável (Cliente)
    $stmtCli = $pdo->prepare("UPDATE tb_clientes SET status_cliente = 'Inativo' WHERE id_cliente = ?");
    $stmtCli->execute([$id_cliente]);

    // 2. Localizar todos os filhos (alunos) deste responsável
    $stmtAlunos = $pdo->prepare("SELECT id_aluno FROM tb_alunos WHERE id_cliente = ?");
    $stmtAlunos->execute([$id_cliente]);
    $alunos = $stmtAlunos->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($alunos)) {
        // Criar uma lista de IDs para usar no IN (?, ?, ...)
        $inQueryAlunos = implode(',', array_fill(0, count($alunos), '?'));

        // 3. Finalizar todos os contratos ATIVOS vinculados a estes alunos
        // Isso impede que novas parcelas continuem sendo geradas/cobradas
        $sqlContratos = "UPDATE tb_contrato 
                         SET status_contrato = 'Finalizado', 
                             dataFinalContrato = CURDATE() 
                         WHERE id_aluno IN ($inQueryAlunos) AND status_contrato = 'Ativo'";
        $stmtContratos = $pdo->prepare($sqlContratos);
        $stmtContratos->execute($alunos); // Passamos o array de IDs dos alunos
    }

    // Se tudo correu bem, salvamos as alterações
    $pdo->commit();
    header("Location: ../index.php?msg=cliente_inativado");
    exit;

} catch (PDOException $e) {
    // Se houver erro, desfazemos tudo
    $pdo->rollBack();
    die("Erro crítico ao inativar cliente e dependências: " . $e->getMessage());
}
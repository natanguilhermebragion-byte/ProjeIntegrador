<?php
require_once __DIR__ . '/../config/conexao.php';

$id = $_GET['id'];
$sql = "SELECT c.*, cl.* FROM tb_contrato c 
        JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente 
        WHERE c.id_contrato = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$dados = $stmt->fetch(PDO::FETCH_OBJ);
?>

<input type="text" name="NomeCompleto" value="<?= $dados->NomeCompleto ?>">
<input type="number" name="valorTotalContrato" value="<?= $dados->valorTotalContrato ?>">
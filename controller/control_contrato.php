<?php
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente         = $_POST['id_cliente'];
    $valorTotal         = (float)$_POST['valorTotalContrato'];
    $qtdParcela         = (int)$_POST['qtdParcela'];
    $dataInicio         = $_POST['dataInicioContrato'];
    $dataFim            = $_POST['dataFinalContrato'];

    
    $valorParcela = $valorTotal / $qtdParcela;

    try {
        $sql = "INSERT INTO tb_contrato (id_cliente, valorParcela, qtdParcela, valorTotalContrato, dataInicioContrato, dataFinalContrato) 
                VALUES (:id_cliente, :vParc, :qtd, :vTotal, :dIni, :dFim)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_cliente' => $id_cliente,
            ':vParc'      => $valorParcela,
            ':qtd'        => $qtdParcela,
            ':vTotal'     => $valorTotal,
            ':dIni'       => $dataInicio,
            ':dFim'       => $dataFim
        ]);

        header("Location: ../view/cad_contrato.php?sucesso=1");
        exit;

    } catch (PDOException $e) {
        die("Erro ao gerar contrato: " . $e->getMessage());
    }
}
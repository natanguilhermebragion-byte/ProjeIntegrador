<?php
$id_contrato = $_GET['id_contrato'] ?? null;
if (!$id_contrato) { header("Location: ../index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Cadastro Realizado • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .sucesso-container {
            max-width: 500px;
            margin: 100px auto;
            text-align: center;
            background: #1e293b;
            padding: 40px;
            border-radius: 15px;
            border: 1px solid #334155;
        }
        .icon-check {
            font-size: 50px;
            color: #10b981;
            margin-bottom: 20px;
        }
        .btn-pdf {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn-pdf:hover { background: #dc2626; transform: scale(1.05); }
        .btn-voltar {
            display: block;
            margin-top: 20px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="sucesso-container">
        <div class="icon-check">✔</div>
        <h2 style="color: white;">Cadastro Concluído!</h2>
        <p style="color: #94a3b8;">O filho e o contrato foram vinculados com sucesso.</p>
        
        <a href="gerar_pdf_contrato.php?id=<?= $id_contrato ?>" target="_blank" class="btn-pdf">
            📄 GERAR CONTRATO (PDF)
        </a>

        <a href="../index.php" class="btn-voltar">Voltar para o Painel</a>
    </div>
</body>
</html>
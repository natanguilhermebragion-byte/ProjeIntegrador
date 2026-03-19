<?php
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tb_clientes WHERE id_cliente = ?");
$stmt->execute([$id]);
$cli = $stmt->fetch(PDO::FETCH_OBJ);

if (!$cli) {
    die("Responsável não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editar Responsável • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .card { max-width: 800px; margin: 30px auto; padding: 25px; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        h2 { color: #3b82f6; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
        label { display: block; color: #94a3b8; font-size: 13px; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; background: #0b1220; border: 1px solid #334155; color: white; border-radius: 8px; margin-bottom: 15px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .btn-save { background: #2563eb; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="card">
            <h2>Editar Dados do Responsável</h2>
            <form action="../controller/control_edit_cliente.php" method="POST">
                <input type="hidden" name="id_cliente" value="<?= $cli->id_cliente ?>">

                <label>Nome Completo</label>
                <input type="text" name="NomeCompleto" value="<?= htmlspecialchars($cli->NomeCompleto) ?>" required>

                <div class="grid">
                    <div><label>CPF</label><input type="text" name="cpf" value="<?= htmlspecialchars($cli->cpf) ?>"></div>
                    <div><label>RG</label><input type="text" name="rg" value="<?= htmlspecialchars($cli->rg) ?>"></div>
                </div>

                <div class="grid">
                    <div><label>E-mail</label><input type="email" name="email" value="<?= htmlspecialchars($cli->email) ?>"></div>
                    <div><label>Telefone</label><input type="text" name="telefone" value="<?= htmlspecialchars($cli->telefone) ?>"></div>
                </div>

                <div class="grid">
                    <div style="grid-column: span 2;"><label>Logradouro</label><input type="text" name="logradouro" value="<?= htmlspecialchars($cli->logradouro) ?>"></div>
                </div>

                <div class="grid">
                    <div><label>Número</label><input type="text" name="numero" value="<?= htmlspecialchars($cli->numero) ?>"></div>
                    <div><label>Bairro</label><input type="text" name="bairro" value="<?= htmlspecialchars($cli->bairro) ?>"></div>
                </div>

                <button type="submit" class="btn-save">SALVAR ALTERAÇÕES</button>
                <a href="../index.php" style="display:block; text-align:center; margin-top:15px; color:#94a3b8; text-decoration:none;">Voltar</a>
            </form>
        </section>
    </div>
</body>
</html>
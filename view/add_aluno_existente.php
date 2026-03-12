<?php require_once __DIR__ . '/../config/conexao.php'; 
$clientes = $pdo->query("SELECT id_cliente, NomeCompleto FROM tb_clientes ORDER BY NomeCompleto")->fetchAll();
$escolas = $pdo->query("SELECT id, nomeEscola FROM tb_escolas ORDER BY nomeEscola")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Adicionar Aluno • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Adicionar Novo Aluno a Cliente Existente</h1>
        <a href="../index.php" style="color: var(--txt); text-decoration: none;">← Voltar</a>
    </header>

    <div class="wrap">
        <section class="card">
            <form action="../controller/control_aluno.php" method="POST">
                <div style="display: grid; gap: 15px;">
                    <label>Selecione o Responsável</label>
                    <select name="id_cliente" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        <?php foreach($clientes as $c): ?>
                            <option value="<?= $c->id_cliente ?>"><?= $c->NomeCompleto ?></option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" name="nomeCompleto" placeholder="Nome do Novo Aluno" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                    <button type="submit" style="background: #059669; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        Vincular Novo Aluno
                    </button>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
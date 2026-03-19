<?php
require_once __DIR__ . '/../config/conexao.php';

// busca clientes e escolas pra deixar os selects do formulário mais "cheios"
$clientes = $pdo->query("SELECT id_cliente, NomeCompleto FROM tb_clientes ORDER BY NomeCompleto")->fetchAll();
$escolas  = $pdo->query("SELECT id, nomeEscola FROM tb_escolas ORDER BY nomeEscola")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Novo Aluno • Projeto Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Cadastro de Aluno</h1>
        <a href="../index.php" style="color: var(--txt); text-decoration: none;">← Voltar</a>
    </header>

    <div class="wrap">
        <?php if (isset($_GET['sucesso'])): ?>
            <div style="background: #059669; color: white; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                Aluno cadastrado com sucesso!
            </div>
        <?php endif; ?>

        <section class="card">
            <form action="../controller/control_aluno.php" method="POST">
                <div style="display: grid; gap: 15px;">
                    <div>
                        <label class="muted" style="display:block; margin-bottom:5px;">Nome do Aluno</label>
                        <input type="text" name="nomeCompleto" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Série</label>
                            <input type="text" name="serie" placeholder="Ex: 6º Ano" style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        </div>
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Sala</label>
                            <input type="text" name="sala" placeholder="Ex: Sala 02" style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Período</label>
                            <select name="periodoDeEstudo" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                                <option value="Manhã">Manhã</option>
                                <option value="Tarde">Tarde</option>
                                <option value="Noite">Noite</option>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Escola</label>
                            <select name="id_escola" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                                <?php foreach($escolas as $e): ?>
                                    <option value="<?= $e->id ?>"><?= htmlspecialchars($e->nomeEscola) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="muted" style="display:block; margin-bottom:5px;">Responsável (Cliente)</label>
                        <select name="id_cliente" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                            <?php foreach($clientes as $c): ?>
                                <option value="<?= $c->id_cliente ?>"><?= htmlspecialchars($c->NomeCompleto) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="margin-top: 10px;">
                        <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            Salvar Aluno
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
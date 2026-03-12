<?php
require_once __DIR__ . '/../config/conexao.php';
// Busca os clientes para o select
$clientes = $pdo->query("SELECT id_cliente, NomeCompleto FROM tb_clientes ORDER BY NomeCompleto")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Novo Contrato • Projeto Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Novo Contrato de Transporte</h1>
        <a href="../index.php" style="color: var(--txt); text-decoration: none;">← Voltar</a>
    </header>

    <div class="wrap">
        <?php if (isset($_GET['sucesso'])): ?>
            <div style="background: #059669; color: white; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                Contrato gerado com sucesso!
            </div>
        <?php endif; ?>

        <section class="card">
            <form action="../controller/control_contrato.php" method="POST">
                <div style="display: grid; gap: 15px;">
                    
                    <div>
                        <label class="muted" style="display:block; margin-bottom:5px;">Responsável (Cliente)</label>
                        <select name="id_cliente" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                            <option value="">Selecione o cliente...</option>
                            <?php foreach($clientes as $c): ?>
                                <option value="<?= $c->id_cliente ?>"><?= htmlspecialchars($c->NomeCompleto) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Valor Total do Contrato</label>
                            <input type="number" step="0.01" name="valorTotalContrato" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        </div>
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Qtd. de Parcelas</label>
                            <input type="number" name="qtdParcela" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Data de Início</label>
                            <input type="date" name="dataInicioContrato" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        </div>
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Data de Término</label>
                            <input type="date" name="dataFinalContrato" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        </div>
                    </div>

                    <div style="margin-top: 10px;">
                        <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            Gerar Contrato
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
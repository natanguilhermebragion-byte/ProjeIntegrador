<?php
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id_contrato = $_GET['id'];

try {
    // 1. Busca todos os dados vinculados (Cliente, Aluno e Contrato)
    $sql = "SELECT c.*, cl.*, a.* FROM tb_contrato c
            JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
            JOIN tb_alunos a ON cl.id_cliente = a.id_cliente
            WHERE c.id_contrato = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_contrato]);
    $dados = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$dados) die("Registro não encontrado.");

    // 2. Busca lista de escolas para o campo de seleção
    $escolas = $pdo->query("SELECT id, nomeEscola FROM tb_escolas ORDER BY nomeEscola")->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editar Cadastro • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .grid-2 { display: flex; gap: 15px; margin-bottom: 10px; }
        .grid-2 > div { flex: 1; }
        h3 { margin-top: 20px; color: #3b82f6; border-bottom: 1px solid #334155; padding-bottom: 5px; font-size: 18px; }
        label { font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px; }
        input, select { width:100%; padding:10px; background:#0b1220; border:1px solid #334155; color:white; border-radius:8px; margin-bottom:10px; }
        .btn-save { background: #059669; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; width:100%; font-size: 16px; margin-top: 20px; }
    </style>
</head>
<body>
    <header>
        <h1>Editar Cadastro Completo</h1>
        <a href="../index.php" style="color: #94a3b8; text-decoration: none;">← Cancelar e Voltar</a>
    </header>

    <div class="wrap" style="max-width: 800px; margin-bottom: 50px;">
        <form action="../controller/control_editar_combinado.php" method="POST">
            <input type="hidden" name="id_contrato" value="<?= $dados->id_contrato ?>">
            <input type="hidden" name="id_cliente" value="<?= $dados->id_cliente ?>">

            <section class="card">
                <h3>Dados do Responsável</h3>
                <label>Nome Completo</label>
                <input type="text" name="NomeCompleto" value="<?= htmlspecialchars($dados->NomeCompleto) ?>" required>
                
                <div class="grid-2">
                    <div><label>CPF</label><input type="text" name="cpf" value="<?= $dados->cpf ?>"></div>
                    <div><label>RG</label><input type="text" name="rg" value="<?= $dados->rg ?>"></div>
                </div>
                
                <div class="grid-2">
                    <div><label>Telefone</label><input type="text" name="telefone" value="<?= $dados->telefone ?>"></div>
                    <div><label>E-mail</label><input type="email" name="email" value="<?= $dados->email ?>"></div>
                </div>

                <h3>Endereço de Atendimento</h3>
                <div class="grid-2">
                    <div style="flex: 1;"><label>CEP</label><input type="text" id="cep" name="cep" value="<?= $dados->cep ?>" onblur="buscaCEP()"></div>
                    <div style="flex: 2;"><label>Logradouro (Rua)</label><input type="text" id="logradouro" name="logradouro" value="<?= $dados->logradouro ?>"></div>
                </div>
                <div class="grid-2">
                    <div><label>Número</label><input type="text" name="numero" value="<?= $dados->numero ?>"></div>
                    <div><label>Bairro</label><input type="text" id="bairro" name="bairro" value="<?= $dados->bairro ?>"></div>
                </div>
            </section>

            <section class="card" style="margin-top:20px;">
                <h3>Dados do Aluno</h3>
                <label>Nome da Criança</label>
                <input type="text" name="nomeAluno" value="<?= htmlspecialchars($dados->nomeCompleto) ?>" required>
                
                <div class="grid-2">
                    <div>
                        <label>Escola</label>
                        <select name="id_escola">
                            <?php foreach($escolas as $e): ?>
                                <option value="<?= $e->id ?>" <?= ($e->id == $dados->id_escola) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e->nomeEscola) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div><label>Série / Sala</label><input type="text" name="serie" value="<?= $dados->serie ?>"></div>
                </div>
            </section>

            <section class="card" style="margin-top:20px; border: 1px solid #f59e0b;">
                <h3 style="color: #f59e0b;">Dados do Contrato e Parcelas</h3>
                <div class="grid-2">
                    <div>
                        <label>Valor Total do Contrato (R$)</label>
                        <input type="number" step="0.01" name="valorTotalContrato" value="<?= $dados->valorTotalContrato ?>">
                    </div>
                    <div>
                        <label>Qtd. Total de Parcelas</label>
                        <input type="number" name="qtdParcela" value="<?= $dados->qtdParcela ?>">
                    </div>
                    <div>
                        <label>Status Geral</label>
                        <select name="status_contrato">
                            <option value="Ativo" <?= ($dados->status_contrato == 'Ativo') ? 'selected' : '' ?>>Ativo</option>
                            <option value="Suspenso" <?= ($dados->status_contrato == 'Suspenso') ? 'selected' : '' ?>>Suspenso</option>
                            <option value="Finalizado" <?= ($dados->status_contrato == 'Finalizado') ? 'selected' : '' ?>>Finalizado</option>
                        </select>
                    </div>
                </div>

                <h4 style="color: #f59e0b; margin-top: 15px; font-size: 14px;">Parcelas Atuais (Ajuste Individual)</h4>
                <p style="font-size: 11px; color: #94a3b8; margin-bottom: 10px;">Dica: Se você mudar a "Qtd. Total" acima, o sistema ajustará o calendário automaticamente ao salvar.</p>
                
                <div style="background: #1e293b; padding: 10px; border-radius: 8px;">
                    <?php
                    $stmtPar = $pdo->prepare("SELECT * FROM tb_calendario WHERE id_contrato = ? ORDER BY numero_parcela ASC");
                    $stmtPar->execute([$id_contrato]);
                    $parcelas = $stmtPar->fetchAll(PDO::FETCH_OBJ);

                    foreach($parcelas as $p): ?>
                        <div class="grid-2" style="align-items: center; border-bottom: 1px solid #334155; padding: 5px 0;">
                            <div style="flex: 0.5; font-weight: bold;">#<?= $p->numero_parcela ?></div>
                            <div style="flex: 2;">
                                <input type="date" name="parcelas[<?= $p->id_calendario ?>][data]" value="<?= $p->data_pagamento ?>" style="margin:0; padding: 5px;">
                            </div>
                            <div style="flex: 2;">
                                <select name="parcelas[<?= $p->id_calendario ?>][status]" style="margin:0; padding: 5px;">
                                    <option value="pendente" <?= ($p->confirmacao_pagamento == 'pendente') ? 'selected' : '' ?>>Pendente</option>
                                    <option value="pago" <?= ($p->confirmacao_pagamento == 'pago') ? 'selected' : '' ?>>Pago</option>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <button type="submit" class="btn-save">ATUALIZAR TUDO (DADOS E PARCELAS)</button>
        </form>
    </div>

    <script>
    function buscaCEP() {
        let cep = document.getElementById('cep').value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(r => r.json())
                .then(d => {
                    if (!("erro" in d)) {
                        document.getElementById('logradouro').value = d.logradouro;
                        document.getElementById('bairro').value = d.bairro;
                    }
                });
        }
    }
    </script>
</body>
</html>
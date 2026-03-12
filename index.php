<?php
require_once 'config/conexao.php'; // Conexão no IP 10.91.45.51

try {
    // 1. Consulta de Contratos (Cruzando dados de cliente e aluno)
    $sqlContratos = "SELECT c.*, cl.NomeCompleto as nome_cliente, cl.id_cliente, a.nomeCompleto as nome_aluno
                 FROM tb_contrato c 
                 LEFT JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
                 LEFT JOIN tb_alunos a ON c.id_cliente = a.id_cliente";
    $resContratos = $pdo->query($sqlContratos);

    // --- CÁLCULOS FINANCEIROS ---
    $totalFaturamento = $pdo->query("SELECT SUM(valorTotalContrato) as total FROM tb_contrato WHERE status_contrato = 'Ativo'")->fetch()->total;

    $mesAtual = date('m');
    $anoAtual = date('Y');
    $sqlMensal = "SELECT SUM(c.valorParcela) as mensal 
                  FROM tb_calendario cal
                  JOIN tb_contrato c ON cal.id_contrato = c.id_contrato
                  WHERE MONTH(cal.data_pagamento) = :mes 
                  AND YEAR(cal.data_pagamento) = :ano
                  AND c.status_contrato = 'Ativo'";

    $stmtMensal = $pdo->prepare($sqlMensal);
    $stmtMensal->execute([':mes' => $mesAtual, ':ano' => $anoAtual]);
    $faturamentoMensal = $stmtMensal->fetch()->mensal ?? 0;

    // 2. Consulta de Pagamentos
    $sqlCalendario = "SELECT cal.*, cl.NomeCompleto as cliente, c.valorParcela 
                      FROM tb_calendario cal
                      JOIN tb_contrato c ON cal.id_contrato = c.id_contrato
                      JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
                      ORDER BY cal.confirmacao_pagamento DESC";
    $resCalendario = $pdo->query($sqlCalendario);

    // 4. Consulta de Alunos
    $sqlAlunos = "SELECT a.*, e.nomeEscola FROM tb_alunos a 
                  LEFT JOIN tb_escolas e ON a.id_escola = e.id";
    $resAlunos = $pdo->query($sqlAlunos);

} catch (PDOException $e) {
    die("<div style='color:white; background:red; padding:10px;'>Erro no banco: " . $e->getMessage() . "</div>");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Administrador • Projeto Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style.css"> 
    <script src="admin.js" defer></script> 
    <style>
        .nav-actions { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .btn-add { background: #3b82f6; color: white; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-pago { background: #059669; color: white; text-decoration: none; padding: 5px 10px; border-radius: 6px; font-size: 12px; }
        .status-pago { color: #10b981; font-weight: bold; }
        .btn-status { padding: 6px; border-radius: 6px; font-size: 14px; color: white; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; min-width: 32px; }
        .resumo-card { background: #1e293b; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; color: white; min-width: 200px; }
        .right { text-align: right; }
        .tab-btn { background: transparent; border: none; color: #94a3b8; padding: 10px 20px; cursor: pointer; font-weight: 600; font-size: 14px; border-bottom: 2px solid transparent; transition: 0.3s; }
        .tab-btn.active { color: #3b82f6; border-bottom: 2px solid #3b82f6; }
        .tab-btn:hover { color: white; }
    </style>
</head>
<body>
    <header>
        <h1>Painel Administrativo</h1>
        <span class="muted">Projeto Registro • Transporte Escolar</span>
    </header>

    <div class="wrap">
        <div class="nav-actions">
            <div style="display: flex; gap: 10px;">
                <a href="view/cad_cliente_aluno.php" class="btn-add" style="background: #2563eb;">+ Novo Cadastro</a>
                <a href="view/add_aluno_existente.php" class="btn-add" style="background: #059669;">+ Adicionar Filho</a>
            </div>

            <div style="display: flex; gap: 15px; margin-left: auto;">
                <div class="resumo-card" style="border-left-color: #3b82f6;">
                    <small style="display:block; opacity:0.7;">Receita Mensal (<?= date('m/Y') ?>)</small>
                    <strong style="font-size: 1.2rem;">R$ <?= number_format($faturamentoMensal ?? 0, 2, ',', '.') ?></strong>
                </div>
                <div class="resumo-card" style="border-left-color: #f59e0b;">
                    <small style="display:block; opacity:0.7;">Faturamento Total Previsto</small>
                    <strong style="font-size: 1.2rem;">R$ <?= number_format($totalFaturamento ?? 0, 2, ',', '.') ?></strong>
                </div>
            </div>
        </div>

        <div class="tabs-menu" style="display: flex; gap: 5px; margin-bottom: 20px; border-bottom: 1px solid #334155;">
            <button class="tab-btn active" onclick="openTab(event, 'aba-pagamentos')">🗓️ Calendário de Pagamentos</button>
            <button class="tab-btn" onclick="openTab(event, 'aba-contratos')">📜 Gestão de Contratos</button>
            <button class="tab-btn" onclick="openTab(event, 'aba-alunos')">🎒 Alunos Matriculados</button>
        </div>

        <div id="aba-pagamentos" class="tab-content">
            <section class="card">
                <div class="toolbar"><h2>Calendário de Pagamentos</h2></div>
                <div style="overflow:auto;">
                    <table id="tbl-pagamentos">
                        <thead>
                            <tr>
                                <th>Contrato</th>
                                <th>Cliente</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($cal = $resCalendario->fetch()): ?>
                            <tr>
                                <td>#<?= $cal->id_contrato ?></td>
                                <td><?= htmlspecialchars($cal->cliente) ?></td>
                                <td>R$ <?= number_format($cal->valorParcela, 2, ',', '.') ?></td>
                                <td><span class="pill"><?= ucfirst($cal->confirmacao_pagamento) ?></span></td>
                                <td>
                                    <?php if($cal->confirmacao_pagamento === 'pendente'): ?>
                                        <a href="controller/control_pagamento.php?id=<?= $cal->id_calendario ?>" class="btn-pago" onclick="return confirm('Confirmar pagamento?')">✔ Marcar Pago</a>
                                    <?php else: ?>
                                        <span class="status-pago">Finalizado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div id="aba-contratos" class="tab-content" style="display:none;">
            <section class="card">
                <div class="toolbar">
                    <h2>Gestão de Contratos</h2>
                    <input type="search" id="busca-contratos" placeholder="Filtrar contratos...">
                </div>
                <div style="overflow:auto;">
                    <table id="tbl-contratos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Aluno</th>
                                <th class="right">Vlr. Parcela</th>
                                <th class="right">Qtd. Parc.</th>
                                <th class="right">Total</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($c = $resContratos->fetch()): 
                                $status = $c->status_contrato ?? 'Ativo'; 
                                $corPill = ($status == 'Finalizado') ? '#334155' : (($status == 'Pendente') ? '#f59e0b' : '#059669');
                            ?>
                            <tr>
                                <td><?= $c->id_contrato ?></td>
                                <td><?= htmlspecialchars($c->nome_cliente) ?></td>
                                <td><?= htmlspecialchars($c->nome_aluno ?? '---') ?></td>
                                <td class="right">R$ <?= number_format($c->valorParcela, 2, ',', '.') ?></td>
                                <td class="right"><?= $c->qtdParcela ?>x</td>
                                <td class="right"><strong>R$ <?= number_format($c->valorTotalContrato, 2, ',', '.') ?></strong></td>
                                <td><span class="pill" style="background: <?= $corPill ?>;"><?= $status ?></span></td>
                                <td style="display: flex; gap: 5px;">
                                    <a href="view/edit_contrato.php?id=<?= $c->id_contrato ?>" class="btn-status" style="background: #6366f1;" title="Editar">✏️</a>
                                    <a href="controller/control_status_contrato.php?id=<?= $c->id_contrato ?>&status=Ativo" class="btn-status" style="background: #059669;" title="Ativar">OK</a>
                                    <a href="controller/control_excluir_geral.php?id_contrato=<?= $c->id_contrato ?>&id_cliente=<?= $c->id_cliente ?>" 
                                       class="btn-status" style="background: #ef4444;" 
                                       onclick="return confirm('ATENÇÃO: Isso excluirá o CONTRATO, as PARCELAS e o CLIENTE. Confirmar?')">🗑️</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div id="aba-alunos" class="tab-content" style="display:none;">
            <section class="card">
                <div class="toolbar">
                    <h2>Alunos Matriculados</h2>
                    <input type="search" id="busca-alunos" placeholder="Filtrar alunos...">
                </div>
                <div style="overflow:auto;">
                    <table id="tbl-alunos">
                        <thead><tr><th>Nome</th><th>Série/Sala</th><th>Escola</th></tr></thead>
                        <tbody>
                            <?php while($a = $resAlunos->fetch()): ?>
                            <tr>
                                <td><?= htmlspecialchars($a->nomeCompleto) ?></td>
                                <td><?= htmlspecialchars($a->serie) ?> / <?= htmlspecialchars($a->sala) ?></td>
                                <td><?= htmlspecialchars($a->nomeEscola ?? 'Não vinculada') ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    </script>
</body>
</html>
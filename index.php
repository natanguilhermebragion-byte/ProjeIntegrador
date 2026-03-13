<?php
require_once 'config/conexao.php'; // Conexão no IP 10.91.45.51

try {
    // 1. Consulta de Contratos
    $sqlContratos = "SELECT c.*, cl.NomeCompleto as nome_cliente, cl.id_cliente, a.nomeCompleto as nome_aluno
                 FROM tb_contrato c 
                 LEFT JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
                 LEFT JOIN tb_alunos a ON c.id_cliente = a.id_cliente";
    $resContratos = $pdo->query($sqlContratos);

    // --- CÁLCULOS FINANCEIROS (APENAS O QUE FALTA RECEBER) ---
    $sqlTotalRestante = "SELECT SUM(c.valorParcela) as total 
                         FROM tb_calendario cal
                         JOIN tb_contrato c ON cal.id_contrato = c.id_contrato
                         WHERE c.status_contrato = 'Ativo' 
                         AND cal.confirmacao_pagamento = 'pendente'";
    $totalFaturamento = $pdo->query($sqlTotalRestante)->fetch()->total ?? 0;

    $mesAtual = date('m');
    $anoAtual = date('Y');
    $sqlMensalPendente = "SELECT SUM(c.valorParcela) as mensal 
                          FROM tb_calendario cal
                          JOIN tb_contrato c ON cal.id_contrato = c.id_contrato
                          WHERE MONTH(cal.data_pagamento) = :mes 
                          AND YEAR(cal.data_pagamento) = :ano
                          AND c.status_contrato = 'Ativo'
                          AND cal.confirmacao_pagamento = 'pendente'";

    $stmtMensal = $pdo->prepare($sqlMensalPendente);
    $stmtMensal->execute([':mes' => $mesAtual, ':ano' => $anoAtual]);
    $faturamentoMensal = $stmtMensal->fetch()->mensal ?? 0;

    // 2. Consulta de Pagamentos ORDENADA POR CLIENTE E NÚMERO DE PARCELA
    $sqlCalendario = "SELECT cal.*, cl.NomeCompleto as cliente, c.valorParcela, c.qtdParcela 
                      FROM tb_calendario cal
                      JOIN tb_contrato c ON cal.id_contrato = c.id_contrato
                      JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
                      ORDER BY cl.NomeCompleto ASC, cal.numero_parcela ASC";
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
        
        /* Accordion Custom Style */
        .accordion-header:hover { background: #334155 !important; }
        .accordion-content { border-top: 1px solid #334155; }
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
                    <small style="display:block; opacity:0.7;">A receber no Mês (<?= date('m/Y') ?>)</small>
                    <strong style="font-size: 1.2rem;">R$ <?= number_format($faturamentoMensal ?? 0, 2, ',', '.') ?></strong>
                </div>
                <div class="resumo-card" style="border-left-color: #f59e0b;">
                    <small style="display:block; opacity:0.7;">Saldo Total a Receber</small>
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
                
                <?php
                // Organizando parcelas por cliente em um array dinâmico
                $agrupadoPorCliente = [];
                while($reg = $resCalendario->fetch(PDO::FETCH_OBJ)) {
                    $agrupadoPorCliente[$reg->cliente][] = $reg;
                }
                ?>

                <div class="accordion-container">
                    <?php foreach($agrupadoPorCliente as $nomeCliente => $parcelas): 
                        // Cria um ID único para o JavaScript (remove espaços e caracteres especiais)
                        $idUnico = "cli_" . preg_replace("/[^a-zA-Z0-9]/", "", $nomeCliente); 
                    ?>
                        <div class="accordion-item" style="margin-bottom: 10px; border: 1px solid #334155; border-radius: 8px; overflow: hidden;">
                            <button class="accordion-header" onclick="toggleAccordion('<?= $idUnico ?>')" style="width: 100%; padding: 15px; background: #1e293b; color: white; border: none; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: bold; transition: 0.2s;">
                                <span>👤 <?= htmlspecialchars($nomeCliente) ?></span>
                                <small style="background: #3b82f6; padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                                    <?= count($parcelas) ?> Parcelas no total
                                </small>
                            </button>

                            <div id="<?= $idUnico ?>" class="accordion-content" style="display: none; background: #0f172a; padding: 10px;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                    <thead>
                                        <tr style="border-bottom: 1px solid #334155; opacity: 0.7;">
                                            <th align="left" style="padding: 8px;">Parcela</th>
                                            <th align="left">Valor</th>
                                            <th align="center">Status</th>
                                            <th align="right">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($parcelas as $p): ?>
                                        <tr style="border-bottom: 1px solid #1e293b;">
                                            <td style="padding: 10px;"><strong><?= $p->numero_parcela ?> / <?= $p->qtdParcela ?></strong></td>
                                            <td>R$ <?= number_format($p->valorParcela, 2, ',', '.') ?></td>
                                            <td align="center">
                                                <span class="pill" style="background: <?= ($p->confirmacao_pagamento == 'confirmado') ? '#059669' : '#f59e0b' ?>; font-size: 10px;">
                                                    <?= ucfirst($p->confirmacao_pagamento) ?>
                                                </span>
                                            </td>
                                            <td align="right">
                                                <?php if($p->confirmacao_pagamento === 'pendente'): ?>
                                                    <a href="controller/control_pagamento.php?id=<?= $p->id_calendario ?>" class="btn-pago" style="padding: 3px 8px;">✔ Pago</a>
                                                <?php else: ?>
                                                    <small style="color: #10b981;">Pago em <?= date('d/m/Y', strtotime($p->data_pagamento)) ?></small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <div id="aba-contratos" class="tab-content" style="display:none;">
            <section class="card">
                <div class="toolbar">
                    <h2>Gestão de Contratos</h2>
                    <input type="search" id="busca-contratos" placeholder="Filtrar pelo nome do cliente..." onkeyup="filtrarContratos()">
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
                                       onclick="return confirm('ATENÇÃO: Isso excluirá o CONTRATO e o CLIENTE. Confirmar?')">🗑️</a>
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
    // Gerenciador de Abas
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

    // Gerenciador do Accordion
    function toggleAccordion(id) {
        var content = document.getElementById(id);
        if (content.style.display === "none") {
            content.style.display = "block";
        } else {
            content.style.display = "none";
        }
    }

    // Filtro de Contratos
    function filtrarContratos() {
        var input = document.getElementById("busca-contratos");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("tbl-contratos");
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[1]; 
            if (td) {
                var txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>
</body>
</html>
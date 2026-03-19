<?php
require_once 'config/auth.php'; 
require_once 'config/conexao.php'; 

// Função para traduzir o mês para Português (abreviado)
function getMesPortugues($data) {
    $meses = [
        "01" => "Jan", "02" => "Fev", "03" => "Mar", "04" => "Abr",
        "05" => "Mai", "06" => "Jun", "07" => "Jul", "08" => "Ago",
        "09" => "Set", "10" => "Out", "11" => "Nov", "12" => "Dez"
    ];
    $mesNumero = date('m', strtotime($data));
    return $meses[$mesNumero] . " / " . date('Y', strtotime($data));
}

try {
    // 1. Consulta de Contratos vinculada estritamente pelo id_aluno
    $sqlContratos = "SELECT c.*, cl.NomeCompleto as nome_cliente, cl.id_cliente, a.nomeCompleto as nome_aluno, a.id_aluno as aluno_id,
                 (SELECT DAY(data_pagamento) FROM tb_calendario WHERE id_contrato = c.id_contrato LIMIT 1) as dia_vencimento
                 FROM tb_contrato c 
                 INNER JOIN tb_alunos a ON c.id_aluno = a.id_aluno
                 LEFT JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
                 ORDER BY cl.NomeCompleto ASC, a.nomeCompleto ASC";
    $resContratos = $pdo->query($sqlContratos);

    // Agrupamento Hierárquico para Gestão de Contratos
    $gestaoContratosHierarquia = [];
    while($row = $resContratos->fetch(PDO::FETCH_OBJ)) {
        $gestaoContratosHierarquia[$row->nome_cliente][$row->nome_aluno][] = $row;
    }

    // --- CÁLCULOS FINANCEIROS ---
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

    // 2. Consulta de Pagamentos - Hierarquia para Calendário
    $sqlCalendario = "SELECT cal.*, cl.NomeCompleto as cliente, a.nomeCompleto as aluno, c.valorParcela, c.qtdParcela 
                      FROM tb_calendario cal
                      JOIN tb_contrato c ON cal.id_contrato = c.id_contrato
                      JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
                      JOIN tb_alunos a ON c.id_aluno = a.id_aluno
                      ORDER BY cl.NomeCompleto ASC, a.nomeCompleto ASC, cal.numero_parcela ASC";
    $resCalendario = $pdo->query($sqlCalendario);

    $hierarquiaPagamentos = [];
    while($reg = $resCalendario->fetch(PDO::FETCH_OBJ)) {
        $hierarquiaPagamentos[$reg->cliente][$reg->aluno][] = $reg;
    }

    // 4. Consulta de Alunos
    $sqlAlunos = "SELECT a.*, e.nomeEscola, cl.NomeCompleto as nome_responsavel 
                  FROM tb_alunos a 
                  LEFT JOIN tb_escolas e ON a.id_escola = e.id
                  LEFT JOIN tb_clientes cl ON a.id_cliente = cl.id_cliente
                  ORDER BY a.nomeCompleto ASC";
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
        .header-main { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-logout { background: #ef4444; color: white; text-decoration: none; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; }
        .nav-actions { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .btn-add { background: #3b82f6; color: white; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: 0.3s; border: none; }
        .btn-add:hover { opacity: 0.8; transform: translateY(-2px); }
        .btn-pago { background: #059669; color: white; text-decoration: none; padding: 5px 10px; border-radius: 6px; font-size: 12px; }
        .btn-status { padding: 6px; border-radius: 6px; font-size: 14px; color: white; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; min-width: 32px; }
        .resumo-card { background: #1e293b; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; color: white; min-width: 200px; }
        .right { text-align: right; }
        .tab-btn { background: transparent; border: none; color: #94a3b8; padding: 10px 20px; cursor: pointer; font-weight: 600; font-size: 14px; border-bottom: 2px solid transparent; transition: 0.3s; }
        .tab-btn.active { color: #3b82f6; border-bottom: 2px solid #3b82f6; }
        .tab-btn:hover { color: white; }
        .actions { display: flex; gap: 8px; align-items: center; }
        .actions a { transition: transform 0.2s; }
        .actions a:hover { transform: scale(1.15); }
        
        .accordion-header:hover { background: #334155 !important; }
        .accordion-content { border-top: 1px solid #334155; }
        .accordion-sub-item { margin-top: 5px; border-left: 4px solid #3b82f6; }
    </style>
</head>
<body>
    <header class="header-main">
        <div>
            <h1>Painel Administrativo</h1>
            <span class="muted">Projeto Registro • Transporte Escolar</span>
        </div>
        <div style="text-align: right;">
            <small style="color: #94a3b8; display: block; margin-bottom: 5px;">Olá, <?= $_SESSION['usuario_nome'] ?></small>
            <a href="controller/control_logout.php" class="btn-logout">Sair do Sistema</a>
        </div>
    </header>

    <div class="wrap">
        <div class="nav-actions">
            <div style="display: flex; gap: 10px;">
                <a href="view/cad_cliente_aluno.php" class="btn-add" style="background: #2563eb;">Novo Cadastro</a>
                <a href="view/add_aluno_existente.php" class="btn-add" style="background: #059669;">Adicionar Filho</a>
                <a href="view/cad_usuario.php" class="btn-add" style="background: #E8AA1E; border: none; color: white;">Novo Usuário</a>
            </div>

            <div style="display: flex; gap: 15px; margin-left: auto;">
                <div class="resumo-card" style="border-left-color: #3b82f6;">
                    <small style="display:block; opacity:0.7;">A receber no Mês (<?= date('m/Y') ?>)</small>
                    <strong style="font-size: 1.2rem;">R$ <?= number_format($faturamentoMensal ?? 0, 2, ',', '.') ?></strong>
                </div>
                <div class="resumo-card" style="border-left-color: #E8AA1E;">
                    <small style="display:block; opacity:0.7;">Saldo Total a Receber</small>
                    <strong style="font-size: 1.2rem;">R$ <?= number_format($totalFaturamento ?? 0, 2, ',', '.') ?></strong>
                </div>
            </div>
        </div>

        <div class="tabs-menu" style="display: flex; gap: 5px; margin-bottom: 20px; border-bottom: 1px solid #334155;">
            <button class="tab-btn active" onclick="openTab(event, 'aba-clientes')">👥 Clientes (Responsáveis)</button>
            <button class="tab-btn" onclick="openTab(event, 'aba-pagamentos')">🗓️ Calendário de Pagamentos</button>
            <button class="tab-btn" onclick="openTab(event, 'aba-contratos')">📜 Gestão de Contratos</button>
            <button class="tab-btn" onclick="openTab(event, 'aba-alunos')">🎒 Alunos Matriculados</button>
            <button class="tab-btn" onclick="openTab(event, 'aba-usuarios')">⚙️ Usuários do Sistema</button>
        </div>

        <div id="aba-clientes" class="tab-content">
            <section class="card">
                <div class="toolbar">
                    <h2>Responsáveis Cadastrados</h2>
                    <input type="search" id="busca-clientes" placeholder="Procurar responsável..." onkeyup="filtrarTabela('tbl-clientes', this.value)">
                </div>
                <div style="overflow:auto;">
                    <table id="tbl-clientes">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CPF / RG</th>
                                <th>Telefone / E-mail</th>
                                <th>Endereço</th>
                                <th class="right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $resClientes = $pdo->query("SELECT * FROM tb_clientes ORDER BY NomeCompleto ASC");
                            while($cli = $resClientes->fetch(PDO::FETCH_OBJ)): 
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($cli->NomeCompleto) ?></strong></td>
                                <td style="font-size: 12px; color: #94a3b8;">
                                    CPF: <?= htmlspecialchars($cli->cpf) ?><br>
                                    RG: <?= htmlspecialchars($cli->rg) ?>
                                </td>
                                <td style="font-size: 12px;">
                                    📞 <?= htmlspecialchars($cli->telefone) ?><br>
                                    📧 <?= htmlspecialchars($cli->email) ?>
                                </td>
                                <td style="font-size: 11px; color: #94a3b8;">
                                    <?= htmlspecialchars($cli->logradouro) ?>, <?= htmlspecialchars($cli->numero) ?><br>
                                    <?= htmlspecialchars($cli->bairro) ?> • CEP: <?= htmlspecialchars($cli->cep) ?>
                                </td>
                                <td class="right actions">
                                    <div style="display: flex; gap: 5px; justify-content: flex-end;">
                                        <a href="view/edit_cliente.php?id=<?= $cli->id_cliente ?>" class="btn-status" style="background: #6366f1;" title="Editar Cliente">✏️</a>
                                        <a href="controller/control_excluir_cliente.php?id=<?= $cli->id_cliente ?>" 
                                           class="btn-status" 
                                           style="background: #ef4444;" 
                                           onclick="return confirm('ATENÇÃO: Isso excluirá o responsável, todos os seus filhos e contratos vinculados. Deseja continuar?')">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div id="aba-pagamentos" class="tab-content" style="display:none;">
            <section class="card">
                <div class="toolbar"><h2>Calendário de Pagamentos</h2></div>
                <div class="accordion-container">
                    <?php foreach($hierarquiaPagamentos as $nomeResponsavel => $filhos): 
                        $idResp = "resp_" . preg_replace("/[^a-zA-Z0-9]/", "", $nomeResponsavel); 
                    ?>
                        <div class="accordion-item" style="margin-bottom: 10px; border: 1px solid #334155; border-radius: 8px;">
                            <button class="accordion-header" onclick="toggleAccordion('<?= $idResp ?>')" style="width: 100%; padding: 15px; background: #1e293b; color: white; border: none; text-align: left; cursor: pointer; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                                <span>👤 CLIENTE: <?= htmlspecialchars($nomeResponsavel) ?></span>
                                <small style="background: #3b82f6; padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                                    <?= count($filhos) ?> Filho(s) vinculado(s)
                                </small>
                            </button>

                            <div id="<?= $idResp ?>" class="accordion-content" style="display: none; padding: 10px; background: #0f172a;">
                                <?php foreach($filhos as $nomeFilho => $parcelas): 
                                    $idFilho = "filho_" . preg_replace("/[^a-zA-Z0-9]/", "", $nomeResponsavel . $nomeFilho); 
                                ?>
                                    <div class="accordion-sub-item">
                                        <button class="accordion-header" onclick="toggleAccordion('<?= $idFilho ?>')" style="width: 100%; padding: 12px; background: #1e293b; color: #94a3b8; border: none; text-align: left; cursor: pointer; font-size: 14px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155;">
                                            <span>🎒 ALUNO: <?= htmlspecialchars($nomeFilho) ?></span>
                                            <small style="opacity: 0.7;"><?= count($parcelas) ?> Parcelas</small>
                                        </button>

                                        <div id="<?= $idFilho ?>" class="accordion-content" style="display: none; padding: 10px; background: #0b1220;">
                                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                                <thead>
                                                    <tr style="border-bottom: 1px solid #334155; opacity: 0.5;">
                                                        <th align="left">Parcela / Vencimento</th>
                                                        <th align="left">Valor</th>
                                                        <th align="center">Status</th>
                                                        <th align="right">Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($parcelas as $p): ?>
                                                        <tr style="border-bottom: 1px solid #1e293b;">
                                                            <td style="padding: 10px;">
                                                                <strong><?= $p->numero_parcela ?> / <?= $p->qtdParcela ?></strong><br>
                                                                <small style="color: #94a3b8;">📅 Vencimento: <?= date('d/m/Y', strtotime($p->data_pagamento)) ?></small>
                                                            </td>
                                                            <td>R$ <?= number_format($p->valorParcela, 2, ',', '.') ?></td>
                                                            <td align="center">
                                                                <span class="pill" style="background: <?= ($p->confirmacao_pagamento == 'confirmado') ? '#059669' : '#f59e0b' ?>;">
                                                                    <?= ucfirst($p->confirmacao_pagamento) ?>
                                                                </span>
                                                            </td>
                                                            <td align="right">
                                                                <?php if($p->confirmacao_pagamento === 'pendente'): ?>
                                                                    <a href="controller/control_pagamento.php?id=<?= $p->id_calendario ?>" class="btn-pago">✔ Pago</a>
                                                                <?php else: ?>
                                                                    <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                                                        <small style="color: #10b981;">Confirmado</small>
                                                                        <a href="controller/control_estorno_pagamento.php?id=<?= $p->id_calendario ?>" style="color: #ef4444; font-size: 10px;" onclick="return confirm('Desfazer?')">Desfazer</a>
                                                                    </div>
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
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <div id="aba-contratos" class="tab-content" style="display:none;">
            <section class="card">
                <div class="toolbar">
                    <h2>Gestão de Contratos</h2>
                    <input type="search" id="busca-contratos" placeholder="Filtrar responsável ou aluno..." onkeyup="filtrarContratosAccordion()">
                </div>
                
                <div class="accordion-container">
                    <?php foreach($gestaoContratosHierarquia as $nomePai => $alunos): 
                        $idSetorPai = "gestao_pai_" . preg_replace("/[^a-zA-Z0-9]/", "", $nomePai); 
                    ?>
                        <div class="accordion-item" style="margin-bottom: 10px; border: 1px solid #334155; border-radius: 8px; overflow: hidden;">
                            <button class="accordion-header" onclick="toggleAccordion('<?= $idSetorPai ?>')" style="width: 100%; padding: 15px; background: #1e293b; color: white; border: none; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: bold;">
                                <span>👤 RESPONSÁVEL: <?= htmlspecialchars($nomePai) ?></span>
                                <small style="background: #3b82f6; padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                                    <?= count($alunos) ?> Aluno(s)
                                </small>
                            </button>

                            <div id="<?= $idSetorPai ?>" class="accordion-content" style="display: none; background: #0f172a; padding: 10px;">
                                
                                <?php foreach($alunos as $nomeFilho => $contratos): 
                                    $idSetorFilho = "gestao_filho_" . preg_replace("/[^a-zA-Z0-9]/", "", $nomePai . $nomeFilho); 
                                ?>
                                    <div class="accordion-sub-item" style="margin-bottom: 8px; border-left: 4px solid #6366f1;">
                                        <button class="accordion-header" onclick="toggleAccordion('<?= $idSetorFilho ?>')" style="width: 100%; padding: 12px; background: #1e293b; color: #94a3b8; border: none; text-align: left; cursor: pointer; font-size: 14px; display: flex; justify-content: space-between; align-items: center;">
                                            <span>🎒 ALUNO: <?= htmlspecialchars($nomeFilho) ?></span>
                                            <small style="opacity: 0.7;"><?= count($contratos) ?> Contrato(s)</small>
                                        </button>

                                        <div id="<?= $idSetorFilho ?>" class="accordion-content" style="display: none; padding: 10px; background: #0b1220;">
                                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                                <thead>
                                                    <tr style="border-bottom: 1px solid #334155; opacity: 0.7;">
                                                        <th align="left">Vigência</th>
                                                        <th align="left">Vencimento</th>
                                                        <th class="right">Parcela</th>
                                                        <th class="right">Total</th>
                                                        <th align="center">Status</th>
                                                        <th align="right">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($contratos as $c): 
                                                        $status = $c->status_contrato ?? 'Ativo'; 
                                                        $corPill = ($status == 'Finalizado') ? '#334155' : (($status == 'Pendente') ? '#f59e0b' : '#059669');
                                                        $inicio = date('d/m/Y', strtotime($c->dataInicioContrato));
                                                        $fim    = (!empty($c->dataFinalContrato)) ? date('d/m/Y', strtotime($c->dataFinalContrato)) : '---';
                                                    ?>
                                                    <tr style="border-bottom: 1px solid #1e293b;">
                                                        <td style="padding: 10px; font-size: 12px;">
                                                            <span style="color: #059669;">Ini: <?= $inicio ?></span><br>
                                                            <span style="color: #ef4444;">Fim: <?= $fim ?></span>
                                                        </td>
                                                        <td style="color: #3b82f6; font-weight: bold;">
                                                            📅 Dia <?= str_pad($c->dia_vencimento, 2, "0", STR_PAD_LEFT) ?>
                                                        </td>
                                                        <td class="right">R$ <?= number_format($c->valorParcela, 2, ',', '.') ?> (<?= $c->qtdParcela ?>x)</td>
                                                        <td class="right"><strong>R$ <?= number_format($c->valorTotalContrato, 2, ',', '.') ?></strong></td>
                                                        <td align="center"><span class="pill" style="background: <?= $corPill ?>; font-size: 10px;"><?= $status ?></span></td>
                                                        <td align="right" class="actions">
                                                            <div style="display: flex; gap: 5px; justify-content: flex-end;">
                                                                <a href="view/edit_contrato.php?id=<?= $c->id_contrato ?>" class="btn-status" style="background: #6366f1;" title="Editar">✏️</a>
                                                                <a href="view/gerar_pdf_contrato.php?id=<?= $c->id_contrato ?>" target="_blank" class="btn-status" style="background: #334155; border: 1px solid #ef4444;" title="PDF">📄</a>
                                                                <a href="controller/control_excluir_geral.php?id_contrato=<?= $c->id_contrato ?>" class="btn-status" style="background: #ef4444;" onclick="return confirm('Excluir Contrato?')">🗑️</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <div id="aba-alunos" class="tab-content" style="display:none;">
            <section class="card">
                <div class="toolbar">
                    <h2>Alunos Matriculados</h2>
                    <input type="search" id="busca-alunos" placeholder="Filtrar alunos..." onkeyup="filtrarTabela('tbl-alunos', this.value)">
                </div>
                <div style="overflow:auto;">
                    <table id="tbl-alunos">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Responsável</th>
                                <th>Série/Sala</th>
                                <th>Escola</th>
                                <th class="right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($a = $resAlunos->fetch(PDO::FETCH_OBJ)): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($a->nomeCompleto) ?></strong></td>
                                <td><?= htmlspecialchars($a->nome_responsavel ?? '---') ?></td>
                                <td><?= htmlspecialchars($a->serie) ?> / <?= htmlspecialchars($a->sala ?? 'S/S') ?></td>
                                <td><?= htmlspecialchars($a->nomeEscola ?? 'Não vinculada') ?></td>
                                <td class="right actions">
                                    <a href="view/edit_aluno_individual.php?id=<?= $a->id_aluno ?>" class="btn-status" style="background: #6366f1;">✏️</a>
                                    <a href="controller/control_excluir_aluno.php?id=<?= $a->id_aluno ?>" class="btn-status" style="background: #ef4444;" onclick="return confirm('Remover Aluno?')">🗑️</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div id="aba-usuarios" class="tab-content" style="display:none;">
            <section class="card">
                <div class="toolbar"><h2>Operadores do Sistema</h2></div>
                <div style="overflow:auto;">
                    <table>
                        <thead><tr><th>Nome</th><th>Login</th><th>E-mail</th><th class="right">Ações</th></tr></thead>
                        <tbody>
                            <?php 
                            $resUsers = $pdo->query("SELECT id_usuario, nome, login, email FROM tb_usuario");
                            while($u = $resUsers->fetch(PDO::FETCH_OBJ)): 
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($u->nome) ?></td>
                                <td><code><?= htmlspecialchars($u->login) ?></code></td>
                                <td><?= htmlspecialchars($u->email) ?></td>
                                <td class="right actions">
                                    <a href="view/edit_usuario.php?id=<?= $u->id_usuario ?>" class="btn-status" style="background: #6366f1;">✏️</a>
                                    <?php if($u->id_usuario != $_SESSION['usuario_id']): ?>
                                        <a href="controller/control_excluir_usuario.php?id=<?= $u->id_usuario ?>" class="btn-status" style="background: #ef4444;" onclick="return confirm('Remover acesso?')">🗑️</a>
                                    <?php endif; ?>
                                </td>
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
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    function toggleAccordion(id) {
        var content = document.getElementById(id);
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
        } else {
            content.style.display = "none";
        }
    }

    function filtrarContratosAccordion() {
        var input = document.getElementById("busca-contratos");
        var filter = input.value.toUpperCase();
        var items = document.querySelectorAll("#aba-contratos .accordion-item");
        items.forEach(function(item) {
            var headerText = item.querySelector(".accordion-header span").textContent || item.querySelector(".accordion-header span").innerText;
            item.style.display = (headerText.toUpperCase().indexOf(filter) > -1) ? "" : "none";
        });
    }

    function filtrarTabela(idTabela, valor) {
        var filter = valor.toUpperCase();
        var table = document.getElementById(idTabela);
        var tr = table.getElementsByTagName("tr");
        for (var i = 1; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[0]; 
            if (td) {
                var txtValue = td.textContent || td.innerText;
                tr[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1) ? "" : "none";
            }
        }
    }
    </script>
</body>
</html>
<?php
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_GET['id'])) { header("Location: ../index.php"); exit; }

$id_contrato = $_GET['id'];

// Busca os dados do contrato, nome do aluno e o dia de vencimento original do calendário
$stmt = $pdo->prepare("SELECT c.*, a.nomeCompleto as aluno, 
                        (SELECT DAY(data_pagamento) FROM tb_calendario WHERE id_contrato = c.id_contrato LIMIT 1) as dia_vencimento
                       FROM tb_contrato c 
                       INNER JOIN tb_alunos a ON c.id_aluno = a.id_aluno 
                       WHERE c.id_contrato = ?");
$stmt->execute([$id_contrato]);
$con = $stmt->fetch(PDO::FETCH_OBJ);

if (!$con) { die("Contrato não encontrado."); }

// Busca as parcelas deste contrato
$parcelas = $pdo->prepare("SELECT * FROM tb_calendario WHERE id_contrato = ? ORDER BY numero_parcela ASC");
$parcelas->execute([$id_contrato]);
$lista_parcelas = $parcelas->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editar Contrato • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .card { max-width: 900px; margin: 30px auto; padding: 25px; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        h2, h3 { color: #3b82f6; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
        label { display: block; color: #94a3b8; font-size: 13px; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; background: #0b1220; border: 1px solid #334155; color: white; border-radius: 8px; margin-bottom: 15px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .btn-save { background: #059669; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; transition: 0.3s; }
        .btn-save:hover { background: #047857; transform: translateY(-2px); }
        .table-parc { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-parc th { text-align: left; color: #94a3b8; font-size: 12px; padding: 10px; }
        .table-parc td { padding: 5px 10px; border-bottom: 1px solid #334155; }
        .info-alerta { background: #451a03; color: #fbbf24; padding: 10px; border-radius: 8px; font-size: 12px; margin-bottom: 20px; border: 1px solid #78350f; }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="card">
            <h2>Editar Contrato - Aluno: <?= htmlspecialchars($con->aluno) ?></h2>
            
            <form action="../controller/control_edit_contrato.php" method="POST">
                <input type="hidden" name="id_contrato" value="<?= $id_contrato ?>">

                <div class="grid-3">
                    <div>
                        <label>Valor Total do Contrato</label>
                        <input type="number" step="0.01" name="valorTotalContrato" value="<?= $con->valorTotalContrato ?>">
                    </div>
                    <div>
                        <label>Qtd. Parcelas</label>
                        <input type="number" name="qtdParcela" value="<?= $con->qtdParcela ?>" min="1" required>
                    </div>
                    <div>
                        <label>Dia de Vencimento Fixo</label>
                        <select name="diaVencimento" required>
                            <?php for($i=1; $i<=28; $i++): ?>
                                <option value="<?= $i ?>" <?= ($con->dia_vencimento == $i) ? 'selected' : '' ?>>Todo dia <?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="grid-3">
                    <div>
                        <label>Data de Início</label>
                        <input type="date" name="dataInicioContrato" value="<?= $con->dataInicioContrato ?>">
                    </div>
                    <div>
                        <label>Data de Término</label>
                        <input type="date" name="dataFinalContrato" value="<?= $con->dataFinalContrato ?>">
                    </div>
                    <div>
                        <label>Status</label>
                        <select name="status_contrato">
                            <option value="Ativo" <?= $con->status_contrato == 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                            <option value="Pendente" <?= $con->status_contrato == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="Finalizado" <?= $con->status_contrato == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="info-alerta">
                    ⚠️ <strong>Atenção:</strong> Se você alterar a <strong>Quantidade de Parcelas</strong>, o calendário atual será removido e novas parcelas serão geradas automaticamente com base na Data de Início e no Dia de Vencimento.
                </div>

                <h3>Manutenção de Datas das Parcelas</h3>
                <p style="color: #94a3b8; font-size: 12px; margin-bottom: 15px;">Use a tabela abaixo para ajustes manuais caso a quantidade de parcelas permaneça a mesma.</p>
                
                <table class="table-parc">
                    <thead>
                        <tr>
                            <th>Parcela</th>
                            <th>Vencimento Atual</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($parcelas as $p): ?>
                            <tr>
                                <td style="color: white;">Parcela <?= $p->numero_parcela ?></td>
                                <td>
                                    <input type="hidden" name="parcelas[<?= $p->id_calendario ?>][id]" value="<?= $p->id_calendario ?>">
                                    <input type="date" name="parcelas[<?= $p->id_calendario ?>][data]" value="<?= $p->data_pagamento ?>" style="margin-bottom: 0; padding: 5px;">
                                </td>
                                <td>
                                    <span class="pill" style="background: <?= $p->confirmacao_pagamento == 'confirmado' ? '#059669' : '#f59e0b' ?>;">
                                        <?= ucfirst($p->confirmacao_pagamento) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 25px;">
                    <button type="submit" class="btn-save">ATUALIZAR CONTRATO E PARCELAS</button>
                    <a href="../index.php" style="display:block; text-align:center; margin-top:15px; color:#94a3b8; text-decoration:none;">Cancelar</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
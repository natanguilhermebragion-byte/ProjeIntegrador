<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id_aluno = $_GET['id'];

try {
    // Busca dados do aluno e o nome do responsável para referência
    $sql = "SELECT a.*, cl.NomeCompleto as nome_responsavel 
            FROM tb_alunos a 
            JOIN tb_clientes cl ON a.id_cliente = cl.id_cliente 
            WHERE a.id_aluno = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_aluno]);
    $aluno = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$aluno) die("Aluno não encontrado.");

    // Busca lista de escolas
    $escolas = $pdo->query("SELECT id, nomeEscola FROM tb_escolas ORDER BY nomeEscola")->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $e) {
    die("Erro ao carregar: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editar Aluno • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .edit-card { max-width: 600px; margin: 40px auto; padding: 25px; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        h2 { color: #3b82f6; margin-bottom: 5px; }
        label { display: block; color: #94a3b8; font-size: 13px; margin-top: 15px; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; background: #0b1220; border: 1px solid #334155; color: white; border-radius: 8px; }
        .grid { display: flex; gap: 15px; }
        .grid > div { flex: 1; }
        .btn-save { background: #059669; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; margin-top: 25px; font-size: 16px; }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="edit-card">
            <h2>Editar Dados do Aluno</h2>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">
                Responsável: <strong><?= htmlspecialchars($aluno->nome_responsavel) ?></strong>
            </p>

            <form action="../controller/control_edit_aluno_individual.php" method="POST">
                <input type="hidden" name="id_aluno" value="<?= $aluno->id_aluno ?>">

                <label>Nome Completo da Criança</label>
                <input type="text" name="nomeCompleto" value="<?= htmlspecialchars($aluno->nomeCompleto) ?>" required>

                <div class="grid">
                    <div>
                        <label>Data de Nascimento</label>
                        <input type="date" name="dataNascimento" value="<?= $aluno->dataNascimento ?>">
                    </div>
                    <div>
                        <label>Série</label>
                        <input type="text" name="serie" value="<?= htmlspecialchars($aluno->serie) ?>">
                    </div>
                </div>

                <div class="grid">
                    <div>
                        <label>Sala/Turma</label>
                        <input type="text" name="sala" value="<?= htmlspecialchars($aluno->sala) ?>">
                    </div>
                    <div>
                        <label>Período</label>
                        <select name="horario_aluno">
                            <option value="Manhã" <?= ($aluno->horario_aluno == 'Manhã') ? 'selected' : '' ?>>Manhã</option>
                            <option value="Tarde" <?= ($aluno->horario_aluno == 'Tarde') ? 'selected' : '' ?>>Tarde</option>
                            <option value="Integral" <?= ($aluno->horario_aluno == 'Integral') ? 'selected' : '' ?>>Integral</option>
                        </select>
                    </div>
                </div>

                <label>Escola</label>
                <select name="id_escola" required>
                    <?php foreach($escolas as $e): ?>
                        <option value="<?= $e->id ?>" <?= ($e->id == $aluno->id_escola) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e->nomeEscola) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Tipo de Transporte</label>
                <select name="tipo_transporte">
                    <option value="Ida e Volta" <?= ($aluno->tipo_transporte == 'Ida e Volta') ? 'selected' : '' ?>>Ida e Volta</option>
                    <option value="Somente Ida" <?= ($aluno->tipo_transporte == 'Somente Ida') ? 'selected' : '' ?>>Somente Ida</option>
                    <option value="Somente Volta" <?= ($aluno->tipo_transporte == 'Somente Volta') ? 'selected' : '' ?>>Somente Volta</option>
                </select>

                <button type="submit" class="btn-save">SALVAR ALTERAÇÕES</button>
                <a href="../index.php" style="display:block; text-align:center; margin-top:15px; color:#94a3b8; text-decoration:none; font-size:13px;">Voltar ao Painel</a>
            </form>
        </section>
    </div>
</body>
</html>
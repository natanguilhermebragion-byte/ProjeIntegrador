<?php 
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id_usuario = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM tb_usuario WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $u = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$u) die("Usuário não encontrado.");
} catch (PDOException $e) {
    die("Erro ao carregar: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Editar Usuário • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .card { max-width: 550px; margin: 50px auto; padding: 25px; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        h2 { color: #3b82f6; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
        label { display: block; color: #94a3b8; font-size: 13px; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; background: #0b1220; border: 1px solid #334155; color: white; border-radius: 8px; margin-bottom: 15px; }
        .grid-flex { display: flex; gap: 15px; }
        .grid-flex > div { flex: 1; }
        .btn-save { background: #3b82f6; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; transition: 0.3s; text-transform: uppercase; }
        .btn-save:hover { background: #2563eb; transform: translateY(-2px); }
        .muted-info { font-size: 11px; color: #64748b; margin-top: -10px; margin-bottom: 15px; display: block; }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="card">
            <h2>Editar Usuário</h2>
            <form action="../controller/control_edit_usuario.php" method="POST">
                <input type="hidden" name="id_usuario" value="<?= $u->id_usuario ?>">

                <label>Nome Completo</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($u->nome) ?>" required>

                <div class="grid-flex">
                    <div>
                        <label>Login (Acesso)</label>
                        <input type="text" name="login" value="<?= htmlspecialchars($u->login) ?>" required>
                    </div>
                    <div>
                        <label>Nova Senha</label>
                        <input type="password" name="senha" placeholder="Manter atual">
                        <small class="muted-info">Preencha apenas se quiser trocar.</small>
                    </div>
                </div>

                <label>E-mail</label>
                <input type="email" name="email" value="<?= htmlspecialchars($u->email) ?>" maxlength="100" required>

                <div class="grid-flex">
                    <div>
                        <label>CPF</label>
                        <input type="text" name="cpf" value="<?= htmlspecialchars($u->cpf) ?>" maxlength="14" required>
                    </div>
                    <div>
                        <label>Telefone</label>
                        <input type="text" name="telefone" value="<?= htmlspecialchars($u->telefone) ?>" maxlength="15">
                    </div>
                </div>

                <button type="submit" class="btn-save">Atualizar Usuário</button>
                <a href="../index.php" style="display:block; text-align:center; margin-top:15px; color:#94a3b8; text-decoration:none; font-size:13px;">Cancelar e Voltar</a>
            </form>
        </section>
    </div>

    <script>
    const handleMasks = {
        cpf(value) {
            return value
                .replace(/\D/g, '')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})/, '$1-$2')
                .replace(/(-\d{2})\d+?$/, '$1');
        },
        telefone(value) {
            return value
                .replace(/\D/g, '')
                .replace(/(\d{2})(\d)/, '($1) $2')
                .replace(/(\d{5})(\d)/, '$1-$2')
                .replace(/(-\d{4})\d+?$/, '$1');
        }
    };

    document.querySelectorAll('input').forEach(($input) => {
        $input.addEventListener('input', (e) => {
            const name = e.target.name.toLowerCase();
            if (name.includes('cpf')) e.target.value = handleMasks.cpf(e.target.value);
            if (name.includes('telefone')) e.target.value = handleMasks.telefone(e.target.value);
        });
    });
    </script>
</body>
</html>
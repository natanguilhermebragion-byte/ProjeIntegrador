<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Novo Usuário • Projeto Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Cadastro de Usuário</h1>
        <a href="../index.php" style="color: var(--txt); text-decoration: none;">← Voltar</a>
    </header>

    <div class="wrap">
        <?php if (isset($_GET['sucesso'])): ?>
            <div style="background: #059669; color: white; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                Usuário cadastrado com sucesso!
            </div>
        <?php endif; ?>

        <section class="card">
            <form action="../controller/control_usuario.php" method="POST">
                <div style="display: grid; gap: 15px;">
                    <div>
                        <label class="muted" style="display:block; margin-bottom:5px;">Nome Completo</label>
                        <input type="text" name="nome" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">CPF</label>
                            <input type="text" name="cpf" placeholder="000.000.000-00" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        </div>
                        <div style="flex: 1;">
                            <label class="muted" style="display:block; margin-bottom:5px;">Telefone</label>
                            <input type="text" name="telefone" placeholder="Somente números" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                        </div>
                    </div>

                    <div>
                        <label class="muted" style="display:block; margin-bottom:5px;">E-mail</label>
                        <input type="email" name="email" required style="width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px;">
                    </div>

                    <div style="margin-top: 10px;">
                        <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            Salvar Usuário
                        </button>
                    </div>
                </div>
            </form> </section>
    </div>
</body>
</html>
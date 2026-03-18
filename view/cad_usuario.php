<?php require_once __DIR__ . '/../config/conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Novo Usuário • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .card { max-width: 550px; margin: 50px auto; padding: 25px; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        h2 { color: #3b82f6; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
        label { display: block; color: #94a3b8; font-size: 13px; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; background: #0b1220; border: 1px solid #334155; color: white; border-radius: 8px; margin-bottom: 15px; }
        .grid-flex { display: flex; gap: 15px; }
        .grid-flex > div { flex: 1; }
        .btn-save { background: #3b82f6; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; transition: 0.3s; }
        .btn-save:hover { background: #2563eb; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="card">
            <h2>Cadastrar Operador</h2>
            <form action="../controller/control_cad_usuario.php" method="POST">
                <label>Nome Completo</label>
                <input type="text" name="nome" required placeholder="Ex: João Silva">

                <div class="grid-flex">
                    <div>
                        <label>Login (Acesso)</label>
                        <input type="text" name="login" required placeholder="Ex: joao.admin">
                    </div>
                    <div>
                        <label>Senha</label>
                        <input type="password" name="senha" required placeholder="Mín. 6 caracteres">
                    </div>
                </div>

                <label>E-mail</label>
                <input type="email" name="email" maxlength="100" placeholder="usuario@email.com" required>

                <div class="grid-flex">
                    <div>
                        <label>CPF</label>
                        <input type="text" name="cpf" maxlength="14" placeholder="000.000.000-00" required>
                    </div>
                    <div>
                        <label>RG</label>
                        <input type="text" name="rg" maxlength="15" placeholder="00.000.000-0">
                    </div>
                </div>

                <label>Telefone</label>
                <input type="text" name="telefone" maxlength="15" placeholder="(00) 00000-0000">

                <button type="submit" class="btn-save">CRIAR USUÁRIO</button>
                <a href="../index.php" style="display:block; text-align:center; margin-top:15px; color:#94a3b8; text-decoration:none; font-size:13px;">Voltar ao Painel</a>
            </form>
        </section>
    </div>

    <script>
    // Objeto com as funções de formatação
    const handleMasks = {
        cpf(value) {
            return value
                .replace(/\D/g, '') // Remove tudo que não é número
                .replace(/(\d{3})(\d)/, '$1.$2') // Coloca ponto após os 3 primeiros números
                .replace(/(\d{3})(\d)/, '$1.$2') // Coloca ponto após os 6 primeiros números
                .replace(/(\d{3})(\d{1,2})/, '$1-$2') // Coloca traço após os 9 primeiros números
                .replace(/(-\d{2})\d+?$/, '$1'); // Limita o fim
        },
        telefone(value) {
            return value
                .replace(/\D/g, '')
                .replace(/(\d{2})(\d)/, '($1) $2') // DDD entre parênteses
                .replace(/(\d{5})(\d)/, '$1-$2') // Traço no celular
                .replace(/(-\d{4})\d+?$/, '$1');
        }
    };

    // Aplica a máscara em tempo real
    document.querySelectorAll('input').forEach(($input) => {
        $input.addEventListener('input', (e) => {
            const name = e.target.name.toLowerCase();
            
            if (name.includes('cpf')) {
                e.target.value = handleMasks.cpf(e.target.value);
            }
            if (name.includes('telefone')) {
                e.target.value = handleMasks.telefone(e.target.value);
            }
        });
    });
    </script>
</body>
</html>
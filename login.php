<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Login • Projeto Registro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-card { max-width: 400px; margin: 100px auto; padding: 30px; background: #1e293b; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h2 { text-align: center; color: #3b82f6; margin-bottom: 25px; }
        .error-msg { background: #ef4444; color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        input { width: 100%; padding: 12px; background: #0b1220; border: 1px solid #334155; color: white; border-radius: 8px; margin-bottom: 20px; box-sizing: border-box; }
        .btn-login { background: #2563eb; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Acesso ao Sistema</h2>
        
        <?php if(isset($_GET['erro'])): ?>
            <div class="error-msg">Usuário ou senha incorretos.</div>
        <?php endif; ?>

        <form action="controller/control_login.php" method="POST">
            <label style="color: #94a3b8; font-size: 13px;">Login</label>
            <input type="text" name="login" required placeholder="Digite seu usuário">

            <label style="color: #94a3b8; font-size: 13px;">Senha</label>
            <input type="password" name="senha" required placeholder="Digite sua senha">

            <button type="submit" class="btn-login">ENTRAR</button>
        </form>
    </div>
</body>
</html>
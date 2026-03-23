<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Cadastrar Escola • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .card-cad { max-width: 500px; margin: 50px auto; padding: 30px; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        h2 { color: #06b6d4; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
        label { display: block; color: #94a3b8; margin-bottom: 5px; font-size: 13px; }
        input { width: 100%; padding: 10px; background: #0b1220; border: 1px solid #334155; color: white; border-radius: 8px; margin-bottom: 15px; outline: none; transition: 0.3s; }
        input:focus { border-color: #06b6d4; }
        .btn-save { background: #06b6d4; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; transition: 0.3s; margin-top: 10px; }
        .btn-save:hover { opacity: 0.8; transform: translateY(-2px); }
        .loading-text { font-size: 11px; color: #06b6d4; display: none; margin-top: -10px; margin-bottom: 10px; font-style: italic; }
    </style>
</head>
<body>
    <div class="card-cad">
        <h2>🏫 Nova Escola</h2>
        <form action="../controller/control_cad_escola.php" method="POST">
            
            <label>Nome da Escola</label>
            <input type="text" name="nomeEscola" required placeholder="Ex: E.E. Maria Ferraz">

            <label>CEP</label>
            <input type="text" name="cep" id="cep" placeholder="00000-000" maxlength="9" required>
            <div id="loading" class="loading-text">Consultando Correios...</div>

            <label>Endereço</label>
            <input type="text" name="enderecoEscola" id="endereco" required placeholder="Rua, Av, etc.">

            <label>Bairro</label>
            <input type="text" name="bairro" id="bairro" required placeholder="Ex: Vila Carmosina">
            
            <button type="submit" class="btn-save">CADASTRAR ESCOLA</button>
            <a href="../index.php" style="display:block; text-align:center; margin-top:15px; color:#94a3b8; text-decoration:none; font-size: 13px;">Cancelar e Voltar</a>
        </form>
    </div>

    <script>
        const cepInput = document.getElementById('cep');
        const enderecoInput = document.getElementById('endereco');
        const bairroInput = document.getElementById('bairro');
        const loading = document.getElementById('loading');

        // Formatação do CEP e gatilho de busca
        cepInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;

            // Dispara busca ao completar os 8 dígitos
            if (value.length === 9) {
                buscarCEP(value.replace('-', ''));
            }
        });

        async function buscarCEP(cep) {
            loading.style.display = 'block';
            
            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();

                if (!data.erro) {
                    enderecoInput.value = data.logradouro;
                    bairroInput.value = data.bairro;
                    // Joga o cursor para o final do endereço para facilitar colocar o número
                    enderecoInput.focus();
                } else {
                    alert("CEP não encontrado!");
                    cepInput.value = "";
                }
            } catch (error) {
                console.error("Erro na busca:", error);
            } finally {
                loading.style.display = 'none';
            }
        }
    </script>
</body>
</html>
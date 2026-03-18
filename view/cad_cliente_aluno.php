<?php 
require_once __DIR__ . '/../config/conexao.php'; 

try {
    $escolas = $pdo->query("SELECT id, nomeEscola FROM tb_escolas ORDER BY nomeEscola")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    $escolas = []; 
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Cadastro Geral • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .grid-2 { display: flex; gap: 15px; margin-bottom: 10px; }
        .grid-2 > div { flex: 1; }
        h3 { margin-top: 20px; color: #3b82f6; border-bottom: 1px solid var(--line); padding-bottom: 5px; font-size: 18px; }
        label { font-size: 12px; margin-left: 5px; }
        input, select { width:100%; padding:10px; background:#0b1220; border:1px solid var(--line); color:white; border-radius:8px; margin-top:5px; }
    </style>
</head>
<body>
    <header>
        <h1>Novo Cadastro Geral</h1>
        <a href="../index.php" style="color: var(--txt); text-decoration: none;">← Voltar</a>
    </header>

    <div class="wrap" style="max-width: 800px; margin-bottom: 50px;">
        <form action="../controller/control_combinado.php" method="POST">
            
            <section class="card">
                <h3>Dados dos Responsáveis</h3>
                <div>
                    <label class="muted">Nome do 1º Responsável</label>
                    <input type="text" name="NomeCompleto" required>
                </div>
                <div class="grid-2">
                    <div>
                        <label class="muted">CPF</label>
                        <input type="text" name="cpf" maxlength="14" placeholder="000.000.000-00" required>
                    </div>
                    <div>
                        <label class="muted">RG</label>
                        <input type="text" name="rg" maxlength="15" placeholder="00.000.000-0" required>
                    </div>
                </div>
                <div class="grid-2">
                    <div>
                        <label class="muted">Telefone</label>
                        <input type="text" name="telefone" maxlength="15" placeholder="(00) 00000-0000" required>
                    </div>
                    <div>
                        <label class="muted">E-mail</label>
                        <input type="email" name="email" maxlength="100" placeholder="exemplo@email.com" required>
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed var(--line);">
                    <label class="muted">Nome do 2º Responsável (Opcional)</label>
                    <input type="text" name="nome_segundo_resp">
                </div>
                <div class="grid-2">
                    <div>
                        <label class="muted">Telefone 2</label>
                        <input type="text" name="telefone_segundo_resp" maxlength="15" placeholder="(00) 00000-0000">
                    </div>
                    <div>
                        <label class="muted">E-mail 2</label>
                        <input type="email" name="email_segundo_resp" maxlength="100" placeholder="exemplo2@email.com">
                    </div>
                </div>

                <h3>Endereço de Atendimento</h3>
                <div class="grid-2">
                    <div style="flex: 1;">
                        <label class="muted">CEP</label>
                        <input type="text" name="cep" id="cep" maxlength="9" onblur="buscaCEP()" placeholder="00000-000" required>
                    </div>
                    <div style="flex: 2;">
                        <label class="muted">Logradouro (Rua)</label>
                        <input type="text" name="logradouro" id="logradouro" placeholder="Preenchimento automático" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div style="flex: 1;">
                        <label class="muted">Número</label>
                        <input type="text" name="numero" id="numero" placeholder="123" required>
                    </div>
                    <div style="flex: 2;">
                        <label class="muted">Bairro</label>
                        <input type="text" name="bairro" id="bairro" placeholder="Preenchimento automático" required>
                    </div>
                </div>

                <div style="margin-top: 10px;">
                    <label class="muted">Complemento / Ponto de Referência</label>
                    <input type="text" name="complemento" id="complemento" placeholder="Ex: Apartamento 12, Próximo ao mercado">
                </div>
            </section>

            <section class="card" style="margin-top:20px;">
                <h3>Dados do Aluno</h3>
                <div class="grid-2">
                    <div style="flex: 2;"><label class="muted">Nome da Criança</label><input type="text" name="nomeAluno" required></div>
                    <div><label class="muted">Data de Nascimento</label><input type="date" name="dataNascimento" required></div>
                </div>
                
                <div class="grid-2">
                    <div>
                        <label class="muted">Escola</label>
                        <select name="id_escola" required>
                            <option value="">Selecione...</option>
                            <?php foreach($escolas as $e): ?>
                                <option value="<?= $e->id ?>"><?= htmlspecialchars($e->nomeEscola) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div><label class="muted">Série</label><input type="text" name="serie"></div>
                    <div><label class="muted">Sala</label><input type="text" name="sala"></div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="muted">Tipo de Serviço</label>
                        <select name="tipo_transporte" required>
                            <option value="Ida e Volta">Ida e Volta</option>
                            <option value="Apenas Ida">Apenas Ida</option>
                            <option value="Apenas Volta">Apenas Volta</option>
                        </select>
                    </div>
                    <div>
                        <label class="muted">Horário/Período</label>
                        <select name="horario_aluno" required>
                            <option value="Manhã">Manhã</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Integral">Integral</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="card" style="margin-top:20px; border: 1px solid #334155;">
                <h3 style="color: #3b82f6; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 15px;">
                    Dados do Contrato Financeiro
                </h3>
                <div class="grid-2">
                    <div>
                        <label>Valor Total do Contrato (R$)</label>
                        <input type="number" step="0.01" name="valorTotalContrato" placeholder="0.00" required>
                    </div>
                    <div>
                        <label>Qtd. de Parcelas</label>
                        <input type="number" name="qtdParcela" id="qtdParcela" value="12" min="1" required>
                    </div>
                </div>

                <div class="grid-2" style="margin-top: 15px;">
                    <div>
                        <label>Data de Início (Vigência)</label>
                        <input type="date" name="dataInicioContrato" id="dataInicio" onchange="calcularDataTermino()" required>
                    </div>
                    <div>
                        <label>Dia de Vencimento Mensal</label>
                        <select name="diaVencimento" id="diaVencimento" required>
                            <?php for($i=1; $i<=28; $i++): ?>
                                <option value="<?= $i ?>" <?= ($i == 10) ? 'selected' : '' ?>>Todo dia <?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <label>Data de Término (Automática)</label>
                    <input type="date" name="dataFinalContrato" id="dataFinal" readonly style="background: #0f172a; color: #94a3b8; cursor: not-allowed;">
                </div>

                <button type="submit" style="margin-top:30px; background: #2563eb; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; width:100%; font-size:16px;">
                    FINALIZAR CADASTRO
                </button>
            </section>
        </form>
    </div>

    <script>
    function buscaCEP() {
        let cep = document.getElementById('cep').value.replace(/\D/g, '');
        if (cep !== "") {
            let validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                document.getElementById('logradouro').value = "...";
                document.getElementById('bairro').value = "...";
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(dados => {
                        if (!("erro" in dados)) {
                            document.getElementById('logradouro').value = dados.logradouro;
                            document.getElementById('bairro').value = dados.bairro;
                            document.getElementById('numero').focus();
                        } else {
                            alert("CEP não encontrado.");
                            document.getElementById('cep').value = "";
                        }
                    })
                    .catch(() => alert("Erro ao buscar o CEP."));
            }
        }
    }

    function calcularDataTermino() {
        const inputInicio = document.getElementById('dataInicio').value;
        const qtdParcelas = parseInt(document.getElementById('qtdParcela').value);
        const inputFinal = document.getElementById('dataFinal');

        if (inputInicio && qtdParcelas > 0) {
            let data = new Date(inputInicio + 'T00:00:00'); 
            data.setMonth(data.getMonth() + (qtdParcelas - 1));

            let ano = data.getFullYear();
            let mes = ("0" + (data.getMonth() + 1)).slice(-2);
            let dia = ("0" + data.getDate()).slice(-2);

            inputFinal.value = `${ano}-${mes}-${dia}`;
        }
    }

    document.getElementById('qtdParcela').addEventListener('input', calcularDataTermino);

    // MÁSCARAS AUTOMÁTICAS
    const masks = {
        cpf(value) {
            return value
                .replace(/\D/g, '')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})/, '$1-$2')
                .replace(/(-\d{2})\d+?$/, '$1');
        },
        tel(value) {
            return value
                .replace(/\D/g, '')
                .replace(/(\d{2})(\d)/, '($1) $2')
                .replace(/(\d{5})(\d)/, '$1-$2')
                .replace(/(-\d{4})\d+?$/, '$1');
        }
    };

    document.querySelectorAll('input').forEach(($input) => {
        const field = $input.name.toLowerCase();

        $input.addEventListener('input', (e) => {
            if (field.includes('cpf')) {
                e.target.value = masks.cpf(e.target.value);
            }
            if (field.includes('telefone') || field.includes('tel')) {
                e.target.value = masks.tel(e.target.value);
            }
        }, false);
    });
    </script>
</body>
</html>
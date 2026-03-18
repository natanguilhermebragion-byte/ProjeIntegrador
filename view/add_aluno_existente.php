<?php 
require_once __DIR__ . '/../config/conexao.php'; 
$clientes = $pdo->query("SELECT id_cliente, NomeCompleto FROM tb_clientes ORDER BY NomeCompleto")->fetchAll(PDO::FETCH_OBJ);
$escolas = $pdo->query("SELECT id, nomeEscola FROM tb_escolas ORDER BY nomeEscola")->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <title>Adicionar Filho e Contrato • Projeto Registro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .card { max-width: 700px; margin: 30px auto; padding: 25px; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        h2, h3 { color: #3b82f6; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
        label { display: block; color: #94a3b8; font-size: 13px; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; background: #0b1220; border: 1px solid #334155; color: white; border-radius: 8px; margin-bottom: 15px; }
        .grid-2 { display: flex; gap: 15px; }
        .grid-2 > div { flex: 1; }
        .btn-save { background: #2563eb; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="card">
            <form action="../controller/control_add_filho.php" method="POST">
                <h2>Dados do Novo Aluno</h2>
                
                <label>Vincular ao Responsável</label>
                <select name="id_cliente" required>
                    <option value="">-- Selecione o Cliente --</option>
                    <?php foreach($clientes as $c): ?>
                        <option value="<?= $c->id_cliente ?>"><?= htmlspecialchars($c->NomeCompleto) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Nome da Criança</label>
                <input type="text" name="nomeAluno" required>

                <div class="grid-2">
                    <div>
                        <label>Escola</label>
                        <select name="id_escola">
                            <option value="">Não informada</option>
                            <?php foreach($escolas as $e): ?>
                                <option value="<?= $e->id ?>"><?= htmlspecialchars($e->nomeEscola) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div><label>Data de Nascimento</label><input type="date" name="dataNascimento" required></div>
                </div>

                <div class="grid-2">
                    <div><label>Série</label><input type="text" name="serie"></div>
                    <div><label>Sala</label><input type="text" name="sala"></div>
                </div>

                <div class="grid-2">
                    <div>
                        <label>Serviço</label>
                        <select name="tipo_transporte">
                            <option value="Ida e Volta">Ida e Volta</option>
                            <option value="Apenas Ida">Apenas Ida</option>
                            <option value="Apenas Volta">Apenas Volta</option>
                        </select>
                    </div>
                    <div>
                        <label>Período</label>
                        <select name="horario_aluno">
                            <option value="Manhã">Manhã</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Integral">Integral</option>
                        </select>
                    </div>
                </div>

                <h3>Dados do Novo Contrato</h3>
                <div class="grid-2">
                    <div><label>Valor Total (R$)</label><input type="number" step="0.01" name="valorTotalContrato" required></div>
                    <div><label>Qtd. Parcelas</label><input type="number" name="qtdParcela" id="qtdParcela" value="12" min="1" required></div>
                </div>

                <div class="grid-2">
                    <div>
                        <label>Data de Início</label>
                        <input type="date" name="dataInicioContrato" id="dataInicio" onchange="calcularDataTermino()" required>
                    </div>
                    <div>
                        <label>Dia de Vencimento</label>
                        <select name="diaVencimento" required>
                            <?php for($i=1; $i<=28; $i++): ?>
                                <option value="<?= $i ?>" <?= ($i == 10) ? 'selected' : '' ?>>Todo dia <?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <label>Data de Término (Automática)</label>
                <input type="date" name="dataFinalContrato" id="dataFinal" readonly style="background: #0f172a; color: #94a3b8;">

                <button type="submit" class="btn-save">CADASTRAR FILHO E GERAR CONTRATO</button>
                <a href="../index.php" style="display:block; text-align:center; margin-top:15px; color:#94a3b8; text-decoration:none; font-size:13px;">Cancelar</a>
            </form>
        </section>
    </div>

    <script>
    function calcularDataTermino() {
        const inputInicio = document.getElementById('dataInicio').value;
        const qtdParcelas = parseInt(document.getElementById('qtdParcela').value);
        const inputFinal = document.getElementById('dataFinal');
        if (inputInicio && qtdParcelas > 0) {
            let data = new Date(inputInicio + 'T00:00:00'); 
            data.setMonth(data.getMonth() + (qtdParcelas - 1));
            inputFinal.value = data.toISOString().split('T')[0];
        }
    }
    document.getElementById('qtdParcela').addEventListener('input', calcularDataTermino);
    </script>
</body>
</html>
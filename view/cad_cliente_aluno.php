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
        .card-financeiro { border: 1px solid #f59e0b !important; }
        .title-fin { color: #f59e0b !important; }
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
                    <div><label class="muted">CPF</label><input type="text" name="cpf" required></div>
                    <div><label class="muted">RG</label><input type="text" name="rg" required></div>
                </div>
                <div class="grid-2">
                    <div><label class="muted">Telefone</label><input type="text" name="telefone" required></div>
                    <div><label class="muted">E-mail</label><input type="email" name="email" required></div>
                </div>

                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed var(--line);">
                    <label class="muted">Nome do 2º Responsável (Opcional)</label>
                    <input type="text" name="nome_segundo_resp">
                </div>
                <div class="grid-2">
                    <div><label class="muted">Telefone 2</label><input type="text" name="telefone_segundo_resp"></div>
                    <div><label class="muted">E-mail 2</label><input type="email" name="email_segundo_resp"></div>
                </div>

                <h3>Endereço de Atendimento</h3>
                <div class="grid-2" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <div style="flex: 3;">
                        <label class="muted">Rua / Logradouro</label>
                        <input type="text" name="logradouro" placeholder="Ex: Rua das Flores" required>
                    </div>
                    <div style="flex: 1;">
                        <label class="muted">Número</label>
                        <input type="text" name="numero" placeholder="123" required>
                    </div>
                </div>

                <div class="grid-2" style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label class="muted">Bairro</label>
                        <input type="text" name="bairro" required>
                    </div>
                    <div style="flex: 1;">
                        <label class="muted">CEP</label>
                        <input type="text" name="cep" required>
                    </div>
                </div>

                <div style="margin-top: 10px;">
                    <label class="muted">Complemento / Ponto de Referência</label>
                    <input type="text" name="complemento" placeholder="Ex: Apartamento 12, Próximo ao mercado">
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

            <section class="card card-financeiro" style="margin-top:20px;">
                <h3 class="title-fin">Dados do Contrato Financeiro</h3>
                <div class="grid-2">
                    <div>
                        <label class="muted">Valor Total do Contrato</label>
                        <input type="number" step="0.01" name="valorTotalContrato" required placeholder="0.00">
                    </div>
                    <div>
                        <label class="muted">Qtd. de Parcelas</label>
                        <input type="number" name="qtdParcela" required placeholder="Ex: 12">
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="muted">Data de Início</label>
                        <input type="date" name="dataInicioContrato" required>
                    </div>
                    <div>
                        <label class="muted">Data de Término</label>
                        <input type="date" name="dataFinalContrato" required>
                    </div>
                </div>

                <button type="submit" style="margin-top:30px; background: #2563eb; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; width:100%; font-size:16px;">
                    FINALIZAR CADASTRO GERAL
                </button>
            </section>
        </form>
    </div>
</body>
</html>
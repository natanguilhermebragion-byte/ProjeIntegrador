<?php $id = $_GET['id_contrato']; ?>
<section class="card" style="text-align: center; max-width: 500px; margin: 50px auto;">
    <h2 style="color: #10b981;">✅ Cadastro Realizado!</h2>
    <p>O cliente, o aluno e o cronograma financeiro foram salvos.</p>
    <br>
    <a href="gerar_pdf_contrato.php?id=<?= $id ?>" target="_blank" 
       style="background: #ef4444; color: white; padding: 15px; border-radius: 8px; text-decoration: none; font-weight: bold; display: block;">
       📄 GERAR CONTRATO PARA ASSINATURA (PDF)
    </a>
    <br>
    <a href="../index.php" style="color: #94a3b8;">Voltar ao Painel</a>
</section>
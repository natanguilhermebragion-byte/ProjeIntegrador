/**
 * admin.js - Lógica do Painel Administrativo
 * Projeto Registro - Transporte Escolar
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. CONFIGURAÇÃO DOS FILTROS DE BUSCA ---
    
    /**
     * Função para filtrar linhas da tabela em tempo real
     * @param {string} idInput - ID do campo de busca <input>
     * @param {string} idTabela - ID da <table> alvo
     */
    const configurarFiltro = (idInput, idTabela) => {
        const input = document.getElementById(idInput);
        const tabela = document.getElementById(idTabela);

        // Verifica se ambos os elementos existem na página atual
        if (!input || !tabela) return;

        const tbody = tabela.getElementsByTagName('tbody')[0];

        input.addEventListener('keyup', () => {
            const termo = input.value.toLowerCase();
            const linhas = tbody.getElementsByTagName('tr');

            for (let linha of linhas) {
                // Pega todo o texto da linha para a busca ser ampla
                const textoLinha = linha.innerText.toLowerCase();
                
                // Se o termo existir em qualquer parte da linha, ela fica visível
                if (textoLinha.includes(termo)) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            }
        });
    };

    // Ativa os filtros relacionando os Inputs com as Tabelas do index.php
    configurarFiltro('busca-contratos', 'tbl-contratos');
    configurarFiltro('busca-alunos', 'tbl-alunos');
    configurarFiltro('busca-clientes', 'tbl-clientes');
    // Caso queira adicionar um input de busca para pagamentos no futuro:
    configurarFiltro('busca-pagamentos', 'tbl-pagamentos');


    

    
    const estilizarStatus = () => {
        const pills = document.querySelectorAll('.pill');
        
        pills.forEach(pill => {
            const status = pill.innerText.toLowerCase().trim();
            
            // Aplica cores baseadas no conteúdo (baseado no seu ENUM do SQL)
            if (status === 'confirmado' || status === 'ativo') {
                pill.style.backgroundColor = '#059669'; // Verde
                pill.style.color = '#ecfdf5';
            } else if (status === 'pendente') {
                pill.style.backgroundColor = '#d97706'; // Laranja
                pill.style.color = '#fffbeb';
            } else if (status === 'cancelado') {
                pill.style.backgroundColor = '#dc2626'; // Vermelho
                pill.style.color = '#fef2f2';
            }
        });
    };

    
    estilizarStatus();
});
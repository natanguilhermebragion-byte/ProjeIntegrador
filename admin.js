/**
 * admin.js - Lógica do Painel Administrativo
 * Projeto Registro - Transporte Escolar
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- Configuração dos filtros de busca ---
    
    /**
     * Função para filtrar linhas da tabela em tempo real
     * @param {string} idInput
     * @param {string} idTabela 
     */
    const configurarFiltro = (idInput, idTabela) => {
        const input = document.getElementById(idInput);
        const tabela = document.getElementById(idTabela);

      
        if (!input || !tabela) return;

        const tbody = tabela.getElementsByTagName('tbody')[0];

        input.addEventListener('keyup', () => {
            const termo = input.value.toLowerCase();
            const linhas = tbody.getElementsByTagName('tr');

            for (let linha of linhas) {
                
                const textoLinha = linha.innerText.toLowerCase();
                
                
                if (textoLinha.includes(termo)) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            }
        });
    };

   
    configurarFiltro('busca-contratos', 'tbl-contratos');
    configurarFiltro('busca-alunos', 'tbl-alunos');
    configurarFiltro('busca-clientes', 'tbl-clientes');
   
    configurarFiltro('busca-pagamentos', 'tbl-pagamentos');


    

    
    const estilizarStatus = () => {
        const pills = document.querySelectorAll('.pill');
        
        pills.forEach(pill => {
            const status = pill.innerText.toLowerCase().trim();
            
            
            if (status === 'confirmado' || status === 'ativo') {
                pill.style.backgroundColor = '#059669';
                pill.style.color = '#ecfdf5';
            } else if (status === 'pendente') {
                pill.style.backgroundColor = '#d97706'; 
                pill.style.color = '#fffbeb';
            } else if (status === 'cancelado') {
                pill.style.backgroundColor = '#dc2626';
                pill.style.color = '#fef2f2';
            }
        });
    };

    
    estilizarStatus();
});
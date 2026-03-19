🚐 Projeto Registro • Transporte Escolar
Sistema de gestão para serviços de transporte escolar, focado no controle de contratos, alunos, responsáveis e automação de pagamentos.



🚀 Funcionalidades Principais


👥 Gestão de Clientes: Cadastro completo de responsáveis com endereços e contatos.

🎒 Controle de Alunos: Vínculo de alunos a escolas, séries, horários e tipos de transporte.

📜 Gestão de Contratos: Criação de contratos com vigência personalizada e geração automática de parcelas.

🗓️ Calendário de Pagamentos: Visualização multinível (Responsável > Aluno > Parcelas) com controle de status (Pendente/Confirmado).

📄 Automação de Documentos: Geração de contrato em PDF via biblioteca Dompdf.

⚙️ Segurança: Sistema de login e controle de usuários do sistema.




🛠️ Tecnologias Utilizadas

Linguagem: PHP 8.0+

Banco de Dados: MySQL / MariaDB

Estilização: CSS3 (Tema Dark Mode Customizado)

Bibliotecas: Dompdf para geração de PDFs.

Arquitetura: Estrutura organizada em view, controller e config.



📦 Como Instalar e Executar
Siga os passos abaixo para rodar o projeto localmente (usando XAMPP, WAMP ou Laragon):



1. Clonar o Repositório
Bash
git clone https://github.com/natanguilhermebragion-byte/ProjeIntegrador
Mova a pasta do projeto para o diretório raiz do seu servidor (ex: C:/xampp/htdocs/).

2. Configurar o Banco de Dados
Acesse o phpMyAdmin.

Crie um novo banco de dados chamado bd_projetoregistro.

Importe o arquivo SQL (se disponível) ou execute as queries de criação das tabelas (tb_clientes, tb_alunos, tb_contrato, tb_calendario, tb_usuario).

3. Configurar Conexão PHP
Abra o arquivo config/conexao.php e ajuste as credenciais se necessário:

PHP
$host = "localhost";
$db   = "bd_projetoregistro";
$user = "root";
$pass = "";
4. Instalar Dependências (Dompdf)
Certifique-se de que a biblioteca Dompdf está na pasta libs/dompdf. Caso não esteja, você pode baixá-la ou instalá-la via Composer:

Bash
composer require dompdf/dompdf
5. Executar o Sistema
Abra o navegador e acesse:
https://github.com/natanguilhermebragion-byte/ProjeIntegrador

🔑 Acesso Padrão
Se o banco de dados for novo, insira um usuário manualmente na tabela tb_usuario ou use o padrão cadastrado:

Login: admin

Senha: (conforme configurado no seu banco)

📁 Estrutura de Pastas
/config: Arquivos de conexão e autenticação.

/controller: Lógica de processamento de dados (exclusão, edição, inserção).

/view: Telas do sistema e formulários.

/libs: Bibliotecas externas (Dompdf).

style.css: Estilização global do painel.

⭐ Desenvolvido para automação e facilidade no transporte escolar.
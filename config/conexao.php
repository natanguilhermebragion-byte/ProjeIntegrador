<?php
// Configurações atualizadas para o novo banco de dados
$host = '10.91.45.51'; // IP corrigido conforme a tua indicação
$db   = 'bd_projetoregistro';
$user = 'admin'; 
$pass = '123456';     

try {
    // Estabelece a conexão usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    // Define o modo de erro e o modo de busca padrão como Objeto
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $e) {
    // Exibe o erro caso a conexão falhe (útil para debugar o IP)
    die("Erro na conexão: " . $e->getMessage());
}
?>
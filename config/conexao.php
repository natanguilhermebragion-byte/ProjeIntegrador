<?php
// configurações do ip da maquina
$host = '10.91.45.51'; 
$db   = 'bd_projetoregistro';
$user = 'admin'; 
$pass = '123456';     

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
   
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $e) {
    // mostra o erro se a conexão falha
    die("Erro na conexão: " . $e->getMessage());
}
?>
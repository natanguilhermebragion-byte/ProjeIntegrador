<?php
session_start();

// Se não existir a sessão de usuário, manda de volta para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
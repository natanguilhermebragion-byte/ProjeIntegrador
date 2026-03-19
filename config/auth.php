<?php
session_start();

// caso nao exista a sessao usuario ele volta pro login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<?php
session_start();

// Forçar login admin
$_SESSION['admin_logado'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_nome'] = 'Admin Sistema';
$_SESSION['admin_acesso'] = 'admin';

// Redirecionar para o admin
header('Location: index.php');
exit();
?>

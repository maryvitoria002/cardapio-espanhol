<?php
session_start();

// Acesso direto ao admin - sem login necessário
$_SESSION['admin_logado'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_nome'] = 'Administrador';
$_SESSION['admin_email'] = 'admin@ecoutesaveur.com';
$_SESSION['admin_cargo'] = 'Administrador';
$_SESSION['login_time'] = time();

// Redirecionar diretamente para o painel
header('Location: index.php');
exit();
?>

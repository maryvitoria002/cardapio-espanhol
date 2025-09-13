<?php
session_start();

echo "<h1>Teste de Sessão Admin</h1>";
echo "<p>Sessões ativas:</p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (!isset($_SESSION['admin_logado'])) {
    echo "<p style='color: red;'>admin_logado não está definido</p>";
} else {
    echo "<p style='color: green;'>admin_logado = " . var_export($_SESSION['admin_logado'], true) . "</p>";
}

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    echo "<p style='color: red;'>Condição de login falharia</p>";
} else {
    echo "<p style='color: green;'>Condição de login passaria</p>";
}
?>

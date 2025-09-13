<?php
// Simular uma sessão de usuário para testar a página inicial
session_start();
$_SESSION['id'] = 1; // Simular usuário logado

// Incluir a página inicial
echo "Iniciando teste da página inicial...\n";
ob_start();

try {
    include 'index.php';
    $output = ob_get_contents();
    echo "Página carregada com sucesso!\n";
    echo "Tamanho do output: " . strlen($output) . " caracteres\n";
} catch (Exception $e) {
    echo "Erro ao carregar página: " . $e->getMessage() . "\n";
} finally {
    ob_end_clean();
}

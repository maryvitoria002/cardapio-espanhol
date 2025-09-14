<?php
require_once 'db/conection.php';

try {
    $database = new Database();
    $pdo = $database->getInstance();
    
    echo "Estrutura da tabela 'pedido':\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->query('DESCRIBE pedido');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        printf("%-20s %-20s %-8s %-8s %-15s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'], 
            $column['Extra']
        );
    }
    
    echo "\n\nStatus únicos na tabela:\n";
    echo str_repeat("-", 40) . "\n";
    
    $stmt = $pdo->query('SELECT DISTINCT status_pedido FROM pedido ORDER BY status_pedido');
    $status = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($status as $s) {
        echo "- " . $s . "\n";
    }
    
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . "\n";
}
?>

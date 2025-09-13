<?php
require_once __DIR__ . '/../../db/conection.php';

try {
    echo "<h3>Estrutura das Tabelas</h3>";
    
    // Verificar tabela pedido
    echo "<h4>Tabela pedido:</h4>";
    $stmt = $conexao->query("DESCRIBE pedido");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
    echo "<h4>Tabela produto_pedido:</h4>";
    $stmt = $conexao->query("DESCRIBE produto_pedido");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
    echo "<h4>Tabela produto:</h4>";
    $stmt = $conexao->query("DESCRIBE produto");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
    echo "<h4>Tabela usuario:</h4>";
    $stmt = $conexao->query("DESCRIBE usuario");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
    // Verificar se existem dados
    echo "<h3>Dados Existentes</h3>";
    
    $stmt = $conexao->query("SELECT COUNT(*) as total FROM pedido");
    $result = $stmt->fetch();
    echo "Total de pedidos: " . $result['total'] . "<br>";
    
    $stmt = $conexao->query("SELECT COUNT(*) as total FROM produto_pedido");
    $result = $stmt->fetch();
    echo "Total de itens de pedido: " . $result['total'] . "<br>";
    
    $stmt = $conexao->query("SELECT COUNT(*) as total FROM produto");
    $result = $stmt->fetch();
    echo "Total de produtos: " . $result['total'] . "<br>";
    
    $stmt = $conexao->query("SELECT COUNT(*) as total FROM usuario");
    $result = $stmt->fetch();
    echo "Total de usuários: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<?php
require_once __DIR__ . '/../../db/conection.php';

try {
    // Inserir alguns pedidos de exemplo
    $sql_pedidos = "INSERT INTO pedido (id_usuario, endereco, modo_pagamento, status_pagamento, data_pedido, status_pedido, motivo_cancelamento, nota) VALUES
        (1, 'Rua das Flores, 123', 'Cartão de Crédito', 'Pago', NOW(), 'Pendente', '', 'Sem cebola'),
        (1, 'Av. Principal, 456', 'PIX', 'Pago', DATE_SUB(NOW(), INTERVAL 1 DAY), 'Entregue', '', ''),
        (1, 'Rua do Centro, 789', 'Dinheiro', 'Pendente', DATE_SUB(NOW(), INTERVAL 2 HOUR), 'Em Preparacao', '', 'Entregar rápido'),
        (1, 'Alameda dos Anjos, 321', 'Cartão de Débito', 'Pago', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Cancelado', 'Cliente desistiu', ''),
        (1, 'Praça da Liberdade, 654', 'PIX', 'Pago', NOW(), 'Pronto', '', 'Sem molho picante')";
    
    $conexao->exec($sql_pedidos);
    
    // Buscar IDs dos pedidos inseridos
    $stmt = $conexao->query("SELECT id_pedido FROM pedido ORDER BY id_pedido DESC LIMIT 5");
    $pedidos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Inserir produtos nos pedidos
    foreach ($pedidos as $index => $id_pedido) {
        $produtos = [
            ['id_produto' => 1, 'quantidade' => 2, 'preco' => 25.90],
            ['id_produto' => 2, 'quantidade' => 1, 'preco' => 35.50],
            ['id_produto' => 3, 'quantidade' => 3, 'preco' => 12.00],
        ];
        
        foreach ($produtos as $produto) {
            $sql_produto = "INSERT INTO produto_pedido (id_pedido, id_produto, quantidade, preco) VALUES (?, ?, ?, ?)";
            $stmt = $conexao->prepare($sql_produto);
            $stmt->execute([$id_pedido, $produto['id_produto'], $produto['quantidade'], $produto['preco']]);
        }
    }
    
    echo "Pedidos de exemplo criados com sucesso!";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>

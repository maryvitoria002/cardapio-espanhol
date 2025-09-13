<?php
require_once __DIR__ . '/../../db/conection.php';

try {
    // Verificar se existe usuario
    $stmt = $conexao->query("SELECT COUNT(*) as total FROM usuario");
    $usuarios = $stmt->fetch()['total'];
    
    if ($usuarios == 0) {
        // Criar usuário básico
        $sql = "INSERT INTO usuario (primeiro_nome, segundo_nome, email, senha) VALUES 
                ('Cliente', 'Teste', 'cliente@teste.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "')";
        $conexao->exec($sql);
        echo "Usuário de teste criado.<br>";
    }
    
    // Verificar se existem produtos
    $stmt = $conexao->query("SELECT COUNT(*) as total FROM produto");
    $produtos = $stmt->fetch()['total'];
    
    if ($produtos == 0) {
        // Criar produtos básicos
        $sql = "INSERT INTO produto (nome, descricao, preco, categoria_id, imagem) VALUES 
                ('Hambúrguer Clássico', 'Hambúrguer com carne, queijo e salada', 25.90, 1, 'hamburguer.jpg'),
                ('Pizza Margherita', 'Pizza tradicional com tomate e mussarela', 35.50, 1, 'pizza.jpg'),
                ('Refrigerante', 'Coca-Cola 350ml', 6.00, 1, 'refrigerante.jpg'),
                ('Batata Frita', 'Porção de batata frita', 12.00, 1, 'batata.jpg'),
                ('Salada Caesar', 'Salada com frango e molho caesar', 18.90, 1, 'salada.jpg')";
        $conexao->exec($sql);
        echo "Produtos de teste criados.<br>";
    }
    
    // Criar categoria se não existir
    $stmt = $conexao->query("SELECT COUNT(*) as total FROM categoria");
    $categorias = $stmt->fetch()['total'];
    
    if ($categorias == 0) {
        $sql = "INSERT INTO categoria (nome, descricao) VALUES ('Pratos Principais', 'Pratos principais do restaurante')";
        $conexao->exec($sql);
        echo "Categoria de teste criada.<br>";
    }
    
    // Agora criar pedidos de teste
    $stmt = $conexao->query("SELECT id_usuario FROM usuario LIMIT 1");
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        $id_usuario = $usuario['id_usuario'];
        
        // Limpar pedidos existentes para não duplicar
        $conexao->exec("DELETE FROM produto_pedido");
        $conexao->exec("DELETE FROM pedido");
        
        // Criar pedidos variados
        $pedidos = [
            ['endereco' => 'Rua das Flores, 123', 'status' => 'Pendente', 'data' => 'NOW()'],
            ['endereco' => 'Av. Principal, 456', 'status' => 'Entregue', 'data' => 'DATE_SUB(NOW(), INTERVAL 1 DAY)'],
            ['endereco' => 'Rua do Centro, 789', 'status' => 'Em Preparacao', 'data' => 'DATE_SUB(NOW(), INTERVAL 2 HOUR)'],
            ['endereco' => 'Alameda dos Anjos, 321', 'status' => 'Cancelado', 'data' => 'DATE_SUB(NOW(), INTERVAL 3 DAY)'],
            ['endereco' => 'Praça da Liberdade, 654', 'status' => 'Pronto', 'data' => 'DATE_SUB(NOW(), INTERVAL 30 MINUTE)']
        ];
        
        foreach ($pedidos as $pedido) {
            $sql = "INSERT INTO pedido (id_usuario, endereco, modo_pagamento, status_pagamento, data_pedido, status_pedido) 
                    VALUES ($id_usuario, '{$pedido['endereco']}', 'PIX', 'Pago', {$pedido['data']}, '{$pedido['status']}')";
            $conexao->exec($sql);
            
            $id_pedido = $conexao->lastInsertId();
            
            // Adicionar produtos aleatórios a cada pedido
            $stmt = $conexao->query("SELECT id_produto, preco FROM produto LIMIT 3");
            $produtos_lista = $stmt->fetchAll();
            
            foreach ($produtos_lista as $produto) {
                $quantidade = rand(1, 3);
                $sql = "INSERT INTO produto_pedido (id_pedido, id_produto, quantidade, preco) 
                        VALUES ($id_pedido, {$produto['id_produto']}, $quantidade, {$produto['preco']})";
                $conexao->exec($sql);
            }
        }
        
        echo "5 pedidos de teste criados com produtos!<br>";
    }
    
    echo "<br><strong>Estrutura do banco criada com sucesso!</strong><br>";
    echo "<a href='index.php'>Voltar para a página de pedidos</a>";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>

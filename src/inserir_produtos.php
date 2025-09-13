<?php
require_once './db/conection.php';

try {
    $database = new Database();
    $pdo = $database->getInstance();
    
    echo "Conexão com banco estabelecida!\n";
    
    // Inserir categorias de exemplo
    $categorias = [
        'Saladas e Bowls',
        'Bebidas e Smoothies', 
        'Massas e Noodles',
        'Entradas e Petiscos',
        'Proteínas e Grelhados',
        'Pães e Sanduíches',
        'Sobremesas'
    ];
    
    echo "Inserindo categorias...\n";
    foreach ($categorias as $categoria) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categoria (nome_categoria) VALUES (?)");
        $stmt->execute([$categoria]);
        echo "Categoria inserida: $categoria\n";
    }
    
    // Buscar IDs das categorias
    $cat_ids = [];
    foreach ($categorias as $categoria) {
        $stmt = $pdo->prepare("SELECT id_categoria FROM categoria WHERE nome_categoria = ?");
        $stmt->execute([$categoria]);
        $id = $stmt->fetchColumn();
        $cat_ids[$categoria] = $id;
        echo "Categoria '$categoria' tem ID: $id\n";
    }
    
    // Inserir produtos de exemplo
    $produtos = [
        ['Salada Caesar', 24.99, 15, 'Disponivel', 'Alface romana fresca, croutons crocantes, parmesão e molho caesar tradicional', 'Saladas_e_bowls/Salada Caesar.jpeg', 1, 'Saladas e Bowls'],
        ['Gaspacho Verde', 18.50, 20, 'Disponivel', 'Sopa fria refrescante com pepino, abacate e ervas finas', 'Bebidas_e_smothies/Gaspacho Verde.png', 1, 'Bebidas e Smoothies'],
        ['Ramen Tori Tamago', 32.90, 12, 'Disponivel', 'Ramen tradicional com caldo de frango, ovo marinado e cebolinha', 'Massas_e_noodles/Ramen Tori Tamago.jpeg', 1, 'Massas e Noodles'],
        ['Bowl de Açaí Tropical', 22.00, 25, 'Disponivel', 'Açaí cremoso com granola, banana, morango e coco ralado', 'Saladas_e_bowls/Bowl de Açaí.jpeg', 1, 'Saladas e Bowls'],
        ['Smoothie Detox Verde', 16.90, 30, 'Disponivel', 'Couve, maçã verde, limão, gengibre e água de coco', 'Bebidas_e_smothies/Smoothie Verde.png', 0, 'Bebidas e Smoothies'],
        ['Pasta al Pesto', 28.50, 18, 'Disponivel', 'Linguine com pesto de manjericão, tomates cherry e pinhões', 'Massas_e_noodles/Pasta Pesto.jpeg', 0, 'Massas e Noodles'],
        ['Bruschetta Caprese', 19.90, 22, 'Disponivel', 'Pão italiano tostado com tomate, mussarela de búfala e manjericão', 'Entradas_e_petiscos/Bruschetta.jpeg', 0, 'Entradas e Petiscos'],
        ['Salmão Grelhado', 45.00, 8, 'Disponivel', 'Salmão fresco grelhado com legumes salteados e molho de ervas', 'Proteinas_e_grelhados/Salmão.jpeg', 1, 'Carnes'],
        ['Sanduíche Natural', 15.50, 35, 'Disponivel', 'Pão integral com peito de peru, queijo branco, alface e tomate', 'Paes,Toasts_e_sanduiches/Sanduiche Natural.jpeg', 0, 'Pães e Sanduíches']
    ];
    
    echo "Inserindo produtos...\n";
    foreach ($produtos as $produto) {
        $stmt = $pdo->prepare("
            INSERT INTO produto 
            (nome_produto, preco, estoque, status, descricao, imagem, eh_popular, id_categoria) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            preco = VALUES(preco),
            estoque = VALUES(estoque),
            status = VALUES(status),
            descricao = VALUES(descricao),
            imagem = VALUES(imagem),
            eh_popular = VALUES(eh_popular)
        ");
        
        $stmt->execute([
            $produto[0], // nome_produto
            $produto[1], // preco
            $produto[2], // estoque
            $produto[3], // status
            $produto[4], // descricao
            $produto[5], // imagem
            $produto[6], // eh_popular
            $cat_ids[$produto[7]] // id_categoria
        ]);
        echo "Produto inserido: " . $produto[0] . "\n";
    }
    
    echo "Produtos inseridos com sucesso!\n";
    
    // Verificar se os produtos foram inseridos
    $stmt = $pdo->query("SELECT COUNT(*) FROM produto");
    $count = $stmt->fetchColumn();
    echo "Total de produtos no banco: $count\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

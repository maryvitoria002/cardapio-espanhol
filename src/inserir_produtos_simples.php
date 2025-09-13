<?php
require_once './db/conection.php';

try {
    $database = new Database();
    $pdo = $database->getInstance();
    
    echo "Inserindo produtos essenciais...\n";
    
    // Produtos usando categorias que já existem
    $produtos = [
        ['Salada Caesar Premium', 24.99, 15, 'Disponivel', 'Alface romana fresca, croutons, parmesão e molho caesar', 'Saladas_e_bowls/Salada Caesar.jpeg', 1, 4], // Bowls
        ['Gaspacho Verde Refrescante', 18.50, 20, 'Disponivel', 'Sopa fria com pepino, abacate e ervas', 'Bebidas_e_smothies/Gaspacho Verde.png', 1, 10], // Bebidas
        ['Ramen Tori Tamago', 32.90, 12, 'Disponivel', 'Ramen com caldo de frango e ovo marinado', 'Massas_e_noodles/Ramen Tori Tamago.jpeg', 1, 8], // Massas
        ['Salmão Grelhado', 45.00, 8, 'Disponivel', 'Salmão fresco grelhado com legumes', 'Proteinas_e_grelhados/Salmão.jpeg', 1, 5], // Carnes
        ['Sanduíche Natural', 15.50, 35, 'Disponivel', 'Pão integral com peito de peru e salada', 'Paes,Toasts_e_sanduiches/Sanduiche Natural.jpeg', 0, 6], // Lanches
        ['Bruschetta Italiana', 19.90, 22, 'Disponivel', 'Pão tostado com tomate e manjericão', 'Entradas_e_petiscos/Bruschetta.jpeg', 0, 9] // Petiscos
    ];
    
    foreach ($produtos as $produto) {
        $stmt = $pdo->prepare("
            INSERT INTO produto 
            (nome_produto, preco, estoque, status, descricao, imagem, eh_popular, id_categoria) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute($produto);
        echo "Produto inserido: " . $produto[0] . "\n";
    }
    
    echo "Produtos inseridos com sucesso!\n";
    
    // Verificar total
    $stmt = $pdo->query("SELECT COUNT(*) FROM produto WHERE status = 'Disponivel'");
    $count = $stmt->fetchColumn();
    echo "Total de produtos disponíveis: $count\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

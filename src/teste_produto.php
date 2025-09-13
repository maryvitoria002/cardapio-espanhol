<?php
require_once './controllers/produto/Crud_produto.php';

try {
    echo "Testando Crud_produto...\n";
    $crudProduto = new Crud_produto();
    echo "Instância criada com sucesso.\n";
    
    $produtos = $crudProduto->read();
    echo "Método read() executado com sucesso.\n";
    echo "Número de produtos encontrados: " . count($produtos) . "\n";
    
    foreach ($produtos as $produto) {
        echo "- ID: " . $produto['id_produto'] . " | Nome: " . $produto['nome_produto'] . " | Preço: R$ " . $produto['preco'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

<?php
require_once '../controllers/produto/Crud_produto.php';

$crudProduto = new Crud_produto();

// Teste de criação de produto
try {
    $result = $crudProduto->create(
        "Teste Produto",          // nome_produto
        "Descrição do produto teste",  // descricao
        25.90,                    // preco
        1,                        // id_categoria (assumindo que existe)
        "",                       // imagem (string vazia em vez de null)
        1                         // ativo (1 = disponível)
    );
    
    if ($result) {
        echo "✅ Produto criado com sucesso!";
    } else {
        echo "❌ Falha ao criar produto";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>

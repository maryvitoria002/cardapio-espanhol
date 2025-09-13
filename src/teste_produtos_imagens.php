<?php
// Teste para verificar produtos na página inicial com imagens
session_start();
$_SESSION['id'] = 1; // Simular usuário logado

require_once './controllers/produto/Crud_produto.php';

try {
    echo "Testando produtos na página inicial com imagens...\n\n";
    
    $crudProduto = new Crud_produto();
    $produtos = $crudProduto->read();
    $produtosDestaque = array_slice($produtos, 0, 6);
    
    echo "=== PRODUTOS EM DESTAQUE NA PÁGINA INICIAL ===\n";
    foreach ($produtosDestaque as $i => $produto) {
        echo ($i + 1) . ". " . $produto['nome_produto'] . "\n";
        echo "   Preço: R$ " . number_format($produto['preco'], 2, ',', '.') . "\n";
        echo "   Estoque: " . $produto['estoque'] . " unidades\n";
        echo "   Imagem: " . ($produto['imagem'] ?: 'SEM IMAGEM') . "\n";
        echo "   Status: " . $produto['status'] . "\n";
        echo "\n";
    }
    
    echo "=== VERIFICAÇÃO DE IMAGENS ===\n";
    $comImagem = 0;
    $semImagem = 0;
    
    foreach ($produtosDestaque as $produto) {
        if (!empty($produto['imagem'])) {
            $caminhoCompleto = "./images/comidas/" . $produto['imagem'];
            if (file_exists($caminhoCompleto)) {
                echo "✅ " . $produto['nome_produto'] . " - Imagem existe\n";
                $comImagem++;
            } else {
                echo "❌ " . $produto['nome_produto'] . " - Imagem não encontrada: " . $caminhoCompleto . "\n";
                $semImagem++;
            }
        } else {
            echo "⚠️  " . $produto['nome_produto'] . " - Sem imagem definida\n";
            $semImagem++;
        }
    }
    
    echo "\n📊 Estatísticas:\n";
    echo "- Produtos com imagem válida: $comImagem\n";
    echo "- Produtos sem imagem ou com imagem inválida: $semImagem\n";
    echo "\n✅ Teste concluído!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>

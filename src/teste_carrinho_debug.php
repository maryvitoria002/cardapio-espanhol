<?php
session_start();

// Simular dados de usuário logado para teste
if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = 1;
    $_SESSION['primeiro_nome'] = 'Teste';
}

// Incluir controlador de produtos
require_once './controllers/produto/Crud_produto.php';

echo "<h2>Teste de Debug do Carrinho</h2>";

// Testar busca de produto
$produtoController = new Crud_produto();
$produto_id = 1; // Testar com ID 1

echo "<h3>Testando busca de produto ID: $produto_id</h3>";

try {
    $dadosProduto = $produtoController->readById($produto_id);
    
    if ($dadosProduto) {
        echo "<p>✅ Produto encontrado:</p>";
        echo "<ul>";
        echo "<li>Nome: " . htmlspecialchars($dadosProduto['nome_produto']) . "</li>";
        echo "<li>Preço: R$ " . number_format($dadosProduto['preco'], 2, ',', '.') . "</li>";
        echo "<li>Estoque: " . $dadosProduto['estoque'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p>❌ Produto não encontrado</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao buscar produto: " . $e->getMessage() . "</p>";
}

// Testar busca de todos os produtos
echo "<h3>Testando busca de todos os produtos</h3>";

try {
    $produtos = $produtoController->read();
    
    if ($produtos && count($produtos) > 0) {
        echo "<p>✅ Encontrados " . count($produtos) . " produtos</p>";
        echo "<ul>";
        foreach (array_slice($produtos, 0, 3) as $produto) {
            echo "<li>ID: " . $produto['id_produto'] . " - " . htmlspecialchars($produto['nome_produto']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Nenhum produto encontrado</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao buscar produtos: " . $e->getMessage() . "</p>";
}

// Mostrar estado atual do carrinho
echo "<h3>Estado atual do carrinho</h3>";
if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
    echo "<p>✅ Carrinho contém " . count($_SESSION['carrinho']) . " itens:</p>";
    echo "<ul>";
    foreach ($_SESSION['carrinho'] as $item) {
        echo "<li>" . htmlspecialchars($item['nome']) . " - Qtd: " . $item['quantidade'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>ℹ️ Carrinho vazio</p>";
}

echo "<br><a href='index.php'>← Voltar para a página inicial</a>";
?>

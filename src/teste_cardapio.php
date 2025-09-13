<?php
// Teste para diagnosticar problemas no cardápio
session_start();
$_SESSION['id'] = 1; // Simular usuário logado

echo "Testando página do cardápio...\n\n";

// Incluir as classes necessárias
require_once './db/conection.php';
require_once './controllers/produto/Crud_produto.php';
require_once './controllers/categoria/Crud_categoria.php';

try {
    // Testar conexão
    $database = new Database();
    $conexao = $database->getInstance();
    echo "✅ Conexão com banco estabelecida\n";
    
    // Testar busca de categorias
    $stmt = $conexao->prepare("SELECT * FROM categoria ORDER BY nome_categoria");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Categorias encontradas: " . count($categorias) . "\n";
    echo "Primeiras 5 categorias:\n";
    for ($i = 0; $i < min(5, count($categorias)); $i++) {
        echo "  - ID: " . $categorias[$i]['id_categoria'] . " | Nome: " . $categorias[$i]['nome_categoria'] . "\n";
    }
    
    // Testar busca de produtos
    $sql = "SELECT p.*, c.nome_categoria as categoria_nome 
            FROM produto p 
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria 
            WHERE p.status = 'Disponivel'
            ORDER BY c.nome_categoria, p.nome_produto";
    
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n✅ Produtos disponíveis encontrados: " . count($produtos) . "\n";
    
    // Organizar produtos por categoria
    $produtos_por_categoria = [];
    foreach ($produtos as $produto) {
        $cat = $produto['categoria_nome'] ?? 'Sem Categoria';
        if (!isset($produtos_por_categoria[$cat])) {
            $produtos_por_categoria[$cat] = [];
        }
        $produtos_por_categoria[$cat][] = $produto;
    }
    
    echo "✅ Produtos organizados por categoria:\n";
    foreach ($produtos_por_categoria as $categoria => $prods) {
        echo "  - $categoria: " . count($prods) . " produtos\n";
    }
    
    // Simular filtro por categoria específica
    echo "\n🔍 Testando filtro por categoria específica...\n";
    $categoria_teste = 'Carnes';
    
    $sql = "SELECT p.*, c.nome_categoria as categoria_nome 
            FROM produto p 
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria 
            WHERE p.status = 'Disponivel' AND c.nome_categoria = :categoria
            ORDER BY p.nome_produto";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':categoria', $categoria_teste);
    $stmt->execute();
    $produtos_filtrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Produtos na categoria '$categoria_teste': " . count($produtos_filtrados) . "\n";
    foreach ($produtos_filtrados as $produto) {
        echo "  - " . $produto['nome_produto'] . " (R$ " . $produto['preco'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>

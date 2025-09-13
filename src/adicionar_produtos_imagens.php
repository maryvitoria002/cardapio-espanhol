<?php
require_once './db/conection.php';

try {
    $database = new Database();
    $pdo = $database->getInstance();
    
    echo "Adicionando produtos novos com imagens específicas...\n\n";
    
    // Produtos novos usando imagens disponíveis
    $novosProdutos = [
        // Carnes Premium
        ['Filé Mignon Dry Aged', 89.90, 5, 'Disponivel', 'Filé mignon maturado por 21 dias, suculento e macio', 'Proteinas_e_grelhados/Carnes dry-aged/filé mignon dry aged.png', 1, 5],
        ['Tomahawk Angus Supreme', 149.90, 3, 'Disponivel', 'Impressionante tomahawk com osso, corte premium', 'Proteinas_e_grelhados/Carnes dry-aged/Tomahawk Angus Supreme.png', 1, 5],
        ['Costela Dry Aged', 69.90, 8, 'Disponivel', 'Costela bovina maturada, sabor intenso e textura única', 'Proteinas_e_grelhados/Carnes dry-aged/costela dry aged.png', 1, 5],
        
        // Ramen Especiais
        ['Don Buri Katso com Ovo', 42.90, 10, 'Disponivel', 'Tigela tradicional japonesa com katsu crocante e ovo', 'Massas_e_noodles/Ramen Artesanal/Don Buri Katso com Ovo.png', 1, 8],
        ['Ramen de Carne Braseada', 46.50, 8, 'Disponivel', 'Ramen com carne braseada lentamente por 8 horas', 'Massas_e_noodles/Ramen Artesanal/Ramen de Carse Braseada.png', 1, 8],
        ['Ramen Katsu Branco', 39.90, 12, 'Disponivel', 'Ramen com caldo branco cremoso e katsu dourado', 'Massas_e_noodles/Ramen Artesanal/Ramen Katsu Branco.png', 1, 8],
        
        // Saladas Gourmet
        ['Salada Mediterrânea de Grão-de-Bico', 28.90, 15, 'Disponivel', 'Salada nutritiva com grão-de-bico, cuscuz e azeitonas', 'Saladas_e_bowls/Saladas vegetarianas/Sala Mediterrânea de Grão-de-Bico e Cuscuz.png', 0, 4],
        ['Salada Crocante de Hortaliças', 24.90, 20, 'Disponivel', 'Mix de hortaliças frescas com molho especial da casa', 'Saladas_e_bowls/Saladas vegetarianas/Salada Crocante de Hortaliças Frescas.png', 0, 4],
        ['Salada Rústica Colorida', 26.50, 18, 'Disponivel', 'Salada artesanal com ingredientes sazonais coloridos', 'Saladas_e_bowls/Saladas vegetarianas/Salada Rústica Colorida.png', 0, 4],
        
        // Petiscos Especiais
        ['Kibe Frito Artesanal', 8.90, 30, 'Disponivel', 'Kibe tradicional feito na casa, crocante por fora', 'Entradas_e_petiscos/Kibe Frito.png', 0, 9],
        ['Tábua de Queijos Selecionados', 45.90, 6, 'Disponivel', 'Seleção de queijos artesanais com acompanhamentos', 'Entradas_e_petiscos/Queijo-na-tabua.png', 1, 9],
        
        // Sorvetes Premium
        ['Sorvete Framboisine Royale', 18.90, 25, 'Disponivel', 'Sorvete gourmet de framboesa com notas reais', 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/Framboisine Royale.png', 1, 3],
        ['Freakshake Celebration', 24.90, 15, 'Disponivel', 'Milkshake extravagante com cobertura especial', 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/Freakshake Celebration.png', 1, 3],
        ['Sorvete Banana Chérie', 16.90, 20, 'Disponivel', 'Sorvete cremoso de banana com toque francês', 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/Banana Chérie.png', 0, 3],
        
        // Temperos e Marinadas
        ['Tempero Asiático Premium', 12.90, 40, 'Disponivel', 'Blend especial de temperos orientais', 'Proteinas_e_grelhados/marinadamais/tempero asiatico.png', 0, 7],
        ['Marinada Tropical', 14.50, 35, 'Disponivel', 'Marinada com frutas tropicais para carnes', 'Proteinas_e_grelhados/marinadamais/marinada tropical.png', 0, 7],
        ['Tempero Defumado', 13.90, 25, 'Disponivel', 'Tempero com notas defumadas para grelhados', 'Proteinas_e_grelhados/marinadamais/tempero defumado.png', 0, 7]
    ];
    
    foreach ($novosProdutos as $produto) {
        $stmt = $pdo->prepare("
            INSERT INTO produto 
            (nome_produto, preco, estoque, status, descricao, imagem, eh_popular, id_categoria) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute($produto);
        echo "✅ Produto adicionado: " . $produto[0] . " (R$ " . $produto[1] . ")\n";
    }
    
    echo "\n=== Estatísticas finais ===\n";
    
    // Contar produtos por categoria
    $stmt = $pdo->query("
        SELECT c.nome_categoria, COUNT(p.id_produto) as total
        FROM categoria c
        LEFT JOIN produto p ON c.id_categoria = p.id_categoria AND p.status = 'Disponivel'
        GROUP BY c.id_categoria, c.nome_categoria
        HAVING total > 0
        ORDER BY total DESC
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['nome_categoria'] . ": " . $row['total'] . " produtos\n";
    }
    
    // Total geral
    $stmt = $pdo->query("SELECT COUNT(*) FROM produto WHERE status = 'Disponivel'");
    $total = $stmt->fetchColumn();
    echo "\n🎯 Total de produtos disponíveis: $total\n";
    
    // Produtos com imagens
    $stmt = $pdo->query("SELECT COUNT(*) FROM produto WHERE status = 'Disponivel' AND imagem IS NOT NULL AND imagem != ''");
    $comImagem = $stmt->fetchColumn();
    echo "📸 Produtos com imagens: $comImagem\n";
    
    echo "\n✅ Produtos adicionados com sucesso!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>

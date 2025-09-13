<?php
require_once './db/conection.php';

try {
    $database = new Database();
    $pdo = $database->getInstance();
    
    echo "Atualizando produtos com imagens reais...\n\n";
    
    // Mapear produtos existentes para imagens disponíveis
    $updates = [
        // Saladas e Bowls
        ['nome_like' => '%Salada Caesar%', 'imagem' => 'Saladas_e_bowls/Saladas vegetarianas/Salada Grega Clássica.png'],
        ['nome_like' => '%Bowl%', 'imagem' => 'Saladas_e_bowls/Saladas vegetarianas/Bowl Saudável de Quinoa e Legumes.png'],
        ['nome_like' => '%Salada Grega%', 'imagem' => 'Saladas_e_bowls/Saladas vegetarianas/Salada Grega Clássica.png'],
        
        // Proteínas - Frangos
        ['nome_like' => '%Frango Grelhado%', 'imagem' => 'Proteinas_e_grelhados/Frangos/frango grelhado.png'],
        ['nome_like' => '%Frango%', 'imagem' => 'Proteinas_e_grelhados/Frangos/frango cozido.png'],
        
        // Proteínas - Carnes
        ['nome_like' => '%Salmão%', 'imagem' => 'Proteinas_e_grelhados/Carnes dry-aged/filé mignon dry aged.png'],
        ['nome_like' => '%Picanha%', 'imagem' => 'Proteinas_e_grelhados/Carnes dry-aged/Ribeye Steak Fatiado.png'],
        
        // Massas e Ramen
        ['nome_like' => '%Ramen%', 'imagem' => 'Massas_e_noodles/Ramen Artesanal/Ramen Clássico com Ovo Mollet.png'],
        ['nome_like' => '%Tori Tamago%', 'imagem' => 'Massas_e_noodles/Ramen Artesanal/Ramen Tori Tamago Verde.png'],
        
        // Entradas e Petiscos
        ['nome_like' => '%Bruschetta%', 'imagem' => 'Entradas_e_petiscos/flat-lay-pieces-bread-with-veggies-removebg-preview.png'],
        ['nome_like' => '%Coxinha%', 'imagem' => 'Entradas_e_petiscos/136164624_7264ecf3-9f07-41a1-86b0-b77ee6d2230e-removebg-preview.png'],
        ['nome_like' => '%Bolinho%', 'imagem' => 'Entradas_e_petiscos/Bolinho-de-bacalhau.png'],
        ['nome_like' => '%Kibe%', 'imagem' => 'Entradas_e_petiscos/Kibe Frito.png'],
        ['nome_like' => '%Queijo%', 'imagem' => 'Entradas_e_petiscos/Queijo-na-tabua.png'],
        
        // Sanduíches
        ['nome_like' => '%Sanduíche%', 'imagem' => 'Entradas_e_petiscos/flat-lay-pieces-bread-with-veggies-removebg-preview (1).png'],
        ['nome_like' => '%Hambúrguer%', 'imagem' => 'Entradas_e_petiscos/IMG_9845-removebg-preview.png'],
        
        // Sobremesas
        ['nome_like' => '%Mousse%', 'imagem' => 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/chocolate.png'],
        ['nome_like' => '%Tiramisu%', 'imagem' => 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/creme sorvete.png'],
        
        // Bebidas e Smoothies
        ['nome_like' => '%Suco%', 'imagem' => 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/morango.png'],
        ['nome_like' => '%Água%', 'imagem' => 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/coco.png'],
        ['nome_like' => '%Smoothie%', 'imagem' => 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/Framboisine Royale.png'],
        ['nome_like' => '%Gaspacho%', 'imagem' => 'Sobremesas_e_doces_gelados/Sorvetes & sobremesas geladas/limão siciliano.png'],
        
        // Patês
        ['nome_like' => '%Patê%', 'imagem' => 'Entradas_e_petiscos/134559078_147cc536-262f-491b-b8d3-f1d3c9d275a0-removebg-preview.png'],
        
        // Molhos
        ['nome_like' => '%Molho%', 'imagem' => 'Proteinas_e_grelhados/marinadamais/marinada mediterranea.png']
    ];
    
    foreach ($updates as $update) {
        $stmt = $pdo->prepare("
            UPDATE produto 
            SET imagem = ? 
            WHERE nome_produto LIKE ? AND (imagem IS NULL OR imagem = '' OR imagem LIKE '%jpeg' OR imagem LIKE '%jpg')
        ");
        
        $stmt->execute([$update['imagem'], $update['nome_like']]);
        $affected = $stmt->rowCount();
        
        if ($affected > 0) {
            echo "✅ Atualizado $affected produto(s) com padrão '{$update['nome_like']}' para imagem '{$update['imagem']}'\n";
        }
    }
    
    echo "\n=== Verificando alguns produtos atualizados ===\n";
    $stmt = $pdo->query("
        SELECT nome_produto, imagem 
        FROM produto 
        WHERE imagem IS NOT NULL AND imagem != '' 
        ORDER BY RAND() 
        LIMIT 10
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['nome_produto'] . " → " . $row['imagem'] . "\n";
    }
    
    echo "\n✅ Atualização de imagens concluída!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>

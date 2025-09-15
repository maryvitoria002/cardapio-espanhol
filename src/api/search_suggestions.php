<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../db/conection.php';

try {
    $query = trim($_GET['q'] ?? '');
    
    if (strlen($query) < 2) {
        echo json_encode([]);
        exit();
    }
    
    $database = new Database();
    $pdo = $database->getInstance();
    
    // Buscar produtos que contenham o termo de pesquisa
    $stmt = $pdo->prepare("
        SELECT 
            p.id_produto,
            p.nome_produto,
            p.descricao,
            p.preco,
            COALESCE(c.nome_categoria, 'Sem categoria') as categoria,
            p.imagem as imagem_url
        FROM produto p
        LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
        WHERE (p.nome_produto LIKE ? OR p.descricao LIKE ? OR c.nome_categoria LIKE ?)
        AND p.estoque > 0 
        AND p.status = 'Disponivel'
        ORDER BY 
            CASE 
                WHEN p.nome_produto LIKE ? THEN 1
                WHEN c.nome_categoria LIKE ? THEN 2
                ELSE 3
            END,
            p.nome_produto
        LIMIT 8
    ");
    
    $searchTerm = "%$query%";
    $priorityTerm = "$query%"; // Para priorizar resultados que começam com o termo
    
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $priorityTerm, $priorityTerm]);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar resultados
    $suggestions = [];
    foreach ($produtos as $produto) {
        $suggestions[] = [
            'id' => $produto['id_produto'],
            'nome' => $produto['nome_produto'],
            'descricao' => substr($produto['descricao'], 0, 60) . '...',
            'preco' => 'R$ ' . number_format($produto['preco'], 2, ',', '.'),
            'categoria' => $produto['categoria'],
            'imagem' => $produto['imagem_url'] ? './images/comidas/' . $produto['imagem_url'] : './assets/default-food.png'
        ];
    }
    
    echo json_encode($suggestions);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
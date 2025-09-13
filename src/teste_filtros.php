<?php
// Teste final da página do cardápio
session_start();
$_SESSION['id'] = 1; // Simular usuário logado

// Simular diferentes parâmetros de categoria
$testes = [
    '',
    'Carnes',
    'Bebidas',
    'Massas'
];

echo "Testando filtros de categoria...\n\n";

foreach ($testes as $categoria_teste) {
    echo "=== Teste: categoria = '$categoria_teste' ===\n";
    
    // Simular $_GET
    $_GET['categoria'] = $categoria_teste;
    
    // Incluir classes necessárias
    require_once './db/conection.php';
    
    try {
        $database = new Database();
        $conexao = $database->getInstance();
        
        // Replicar lógica do cardápio.php
        $categoria_selecionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';
        
        $sql = "SELECT p.*, c.nome_categoria as categoria_nome 
                FROM produto p 
                LEFT JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE p.status = 'Disponivel'";
        
        $params = [];
        
        if (!empty($categoria_selecionada)) {
            $sql .= " AND c.nome_categoria = :categoria";
            $params['categoria'] = $categoria_selecionada;
        }
        
        $sql .= " ORDER BY c.nome_categoria, p.nome_produto";
        
        $stmt = $conexao->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Produtos encontrados: " . count($produtos) . "\n";
        
        if (count($produtos) > 0) {
            echo "Primeiros 3 produtos:\n";
            for ($i = 0; $i < min(3, count($produtos)); $i++) {
                echo "  - " . $produtos[$i]['nome_produto'] . " (" . $produtos[$i]['categoria_nome'] . ")\n";
            }
        }
        
        echo "\n";
        
    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n\n";
    }
    
    // Limpar $_GET para próximo teste
    unset($_GET['categoria']);
}

echo "✅ Todos os testes de filtro concluídos!\n";
?>

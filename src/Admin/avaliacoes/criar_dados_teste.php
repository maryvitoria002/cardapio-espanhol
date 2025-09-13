<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../db/conection.php';

try {
    $database = new Database();
    $conexao = $database->getInstance();
    
    echo "<h2>Criando Dados de Teste para Avaliações</h2>";
    
    // Verificar se existe usuário de teste
    $stmt = $conexao->prepare("SELECT id_usuario FROM usuario LIMIT 1");
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo "<p>Criando usuário de teste...</p>";
        $sql = "INSERT INTO usuario (primeiro_nome, segundo_nome, email, password, telefone, data_criacao) 
                VALUES ('Maria', 'Silva', 'maria@teste.com', '123456', '11888888888', NOW())";
        $conexao->exec($sql);
        
        $stmt = $conexao->prepare("SELECT id_usuario FROM usuario LIMIT 1");
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    $id_usuario = $usuario['id_usuario'];
    
    // Verificar se existe pedido de teste
    $stmt = $conexao->prepare("SELECT id_pedido FROM pedido LIMIT 1");
    $stmt->execute();
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedido) {
        echo "<p>Criando pedido de teste...</p>";
        $sql = "INSERT INTO pedido (id_usuario, endereco, modo_pagamento, status_pagamento, status_pedido, data_pedido) 
                VALUES ($id_usuario, 'Rua Teste, 123', 'Cartão', 'Pago', 'Entregue', NOW() - INTERVAL 1 DAY)";
        $conexao->exec($sql);
        
        $stmt = $conexao->prepare("SELECT id_pedido FROM pedido LIMIT 1");
        $stmt->execute();
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    $id_pedido = $pedido['id_pedido'];
    
    // Verificar quantas avaliações existem
    $stmt = $conexao->prepare("SELECT COUNT(*) as total FROM avaliacao");
    $stmt->execute();
    $avaliacoes_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p>Avaliações existentes: $avaliacoes_count</p>";
    
    if ($avaliacoes_count < 5) {
        echo "<p>Criando avaliações de teste...</p>";
        
        $avaliacoes_teste = [
            [
                'nota' => 5,
                'texto' => 'Excelente comida! O hambúrguer estava delicioso e o atendimento foi perfeito.',
                'status' => 'Aprovada'
            ],
            [
                'nota' => 4,
                'texto' => 'Muito bom! A entrega foi rápida e a comida chegou quentinha.',
                'status' => 'Aprovada'
            ],
            [
                'nota' => 3,
                'texto' => 'A comida estava boa, mas a entrega demorou mais que o esperado.',
                'status' => 'Pendente'
            ],
            [
                'nota' => 5,
                'texto' => 'Simplesmente perfeito! Recomendo a todos. Voltarei sempre!',
                'status' => 'Aprovada'
            ],
            [
                'nota' => 2,
                'texto' => 'A comida chegou fria e o sabor não estava muito bom.',
                'status' => 'Pendente'
            ]
        ];
        
        foreach ($avaliacoes_teste as $i => $avaliacao) {
            $sql = "INSERT INTO avaliacao (nota, texto_avaliacao, data_avaliacao, status, id_pedido, id_usuario) 
                    VALUES ({$avaliacao['nota']}, '{$avaliacao['texto']}', NOW() - INTERVAL $i HOUR, '{$avaliacao['status']}', $id_pedido, $id_usuario)";
            $conexao->exec($sql);
            
            echo "<p style='color: blue;'>✅ Avaliação {$avaliacao['nota']} estrelas criada ({$avaliacao['status']})</p>";
        }
    }
    
    echo "<h3>Dados finais:</h3>";
    
    // Mostrar estatísticas
    $stmt = $conexao->prepare("SELECT COUNT(*) as total FROM avaliacao");
    $stmt->execute();
    echo "<p>Total de avaliações: " . $stmt->fetch(PDO::FETCH_ASSOC)['total'] . "</p>";
    
    $stmt = $conexao->prepare("SELECT AVG(nota) as media FROM avaliacao");
    $stmt->execute();
    $media = $stmt->fetch(PDO::FETCH_ASSOC)['media'];
    echo "<p>Média das avaliações: " . round($media, 1) . " estrelas</p>";
    
    $stmt = $conexao->prepare("SELECT status, COUNT(*) as total FROM avaliacao GROUP BY status");
    $stmt->execute();
    $status_count = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Por status:</p>";
    echo "<ul>";
    foreach ($status_count as $status) {
        echo "<li>{$status['status']}: {$status['total']}</li>";
    }
    echo "</ul>";
    
    echo "<p style='color: green; font-weight: bold;'>✅ Dados de teste para avaliações criados com sucesso!</p>";
    echo "<p><a href='index.php'>Ir para página de avaliações</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>

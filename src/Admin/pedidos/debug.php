<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Debug da Página de Pedidos</h2>";

// Testar conexão com banco
try {
    require_once __DIR__ . '/../../db/conection.php';
    $database = new Database();
    $conexao = $database->getInstance();
    echo "<p style='color: green;'>✅ Conexão com banco OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na conexão: " . $e->getMessage() . "</p>";
    exit;
}

// Testar classe Crud_pedido
try {
    require_once __DIR__ . '/../../controllers/pedido/Crud_pedido.php';
    $crudPedido = new Crud_pedido($conexao);
    echo "<p style='color: green;'>✅ Classe Crud_pedido OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na classe: " . $e->getMessage() . "</p>";
    exit;
}

// Testar métodos básicos
try {
    $total = $crudPedido->count();
    echo "<p style='color: green;'>✅ Método count() OK: $total pedidos</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro no count(): " . $e->getMessage() . "</p>";
}

try {
    $pedidos = $crudPedido->readAll();
    echo "<p style='color: green;'>✅ Método readAll() OK: " . count($pedidos) . " pedidos retornados</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro no readAll(): " . $e->getMessage() . "</p>";
}

// Listar estrutura da tabela
try {
    $stmt = $conexao->prepare("DESCRIBE pedido");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Estrutura da tabela 'pedido':</h3>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao listar colunas: " . $e->getMessage() . "</p>";
}

// Verificar se existem pedidos
try {
    $stmt = $conexao->prepare("SELECT COUNT(*) as total FROM pedido");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total de pedidos na base: " . $result['total'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao contar pedidos: " . $e->getMessage() . "</p>";
}
?>

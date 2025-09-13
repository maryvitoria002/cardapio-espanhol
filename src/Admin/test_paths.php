<?php
echo "<h2>Teste de Caminhos - Admin</h2>";

// Testar caminhos dos controllers
$controllers = [
    'Crud_usuario' => __DIR__ . '/../controllers/usuario/Crud_usuario.php',
    'Crud_produto' => __DIR__ . '/../controllers/produto/Crud_produto.php',
    'Crud_pedido' => __DIR__ . '/../controllers/pedido/Crud_pedido.php',
    'Crud_funcionario' => __DIR__ . '/../controllers/funcionario/Crud_funcionario.php',
    'Crud_categoria' => __DIR__ . '/../controllers/categoria/Crud_categoria.php'
];

foreach ($controllers as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name: $path<br>";
    } else {
        echo "❌ $name: $path (NÃO ENCONTRADO)<br>";
    }
}

echo "<br><h3>Teste de Include:</h3>";

try {
    require_once __DIR__ . '/../controllers/usuario/Crud_usuario.php';
    echo "✅ Crud_usuario incluído com sucesso<br>";
    
    require_once __DIR__ . '/../controllers/produto/Crud_produto.php';
    echo "✅ Crud_produto incluído com sucesso<br>";
    
    require_once __DIR__ . '/../controllers/pedido/Crud_pedido.php';
    echo "✅ Crud_pedido incluído com sucesso<br>";
    
    require_once __DIR__ . '/../controllers/funcionario/Crud_funcionario.php';
    echo "✅ Crud_funcionario incluído com sucesso<br>";
    
    require_once __DIR__ . '/../controllers/categoria/Crud_categoria.php';
    echo "✅ Crud_categoria incluído com sucesso<br>";
    
    $crudUsuario = new Crud_usuario();
    echo "✅ Objeto Crud_usuario criado com sucesso<br>";
    
    $crudProduto = new Crud_produto();
    echo "✅ Objeto Crud_produto criado com sucesso<br>";
    
    $crudPedido = new Crud_pedido();
    echo "✅ Objeto Crud_pedido criado com sucesso<br>";
    
    $crudFuncionario = new Crud_funcionario();
    echo "✅ Objeto Crud_funcionario criado com sucesso<br>";
    
    $crudCategoria = new Crud_categoria();
    echo "✅ Objeto Crud_categoria criado com sucesso<br>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Estrutura de Diretórios:</h3>";
echo "Diretório atual: " . __DIR__ . "<br>";
echo "Diretório pai: " . dirname(__DIR__) . "<br>";
echo "Controllers path: " . __DIR__ . '/../controllers/' . "<br>";
?>

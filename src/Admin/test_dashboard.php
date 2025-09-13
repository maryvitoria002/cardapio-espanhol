<?php
// Teste simples para verificar as conexões e métodos
require_once 'controllers/funcionario/Crud_funcionario.php';
require_once 'controllers/usuario/Crud_usuario.php';
require_once 'controllers/produto/Crud_produto.php';
require_once 'controllers/pedido/Crud_pedido.php';
require_once 'controllers/categoria/Crud_categoria.php';

try {
    echo "Testando conexões...\n";
    
    $crudFuncionario = new Crud_funcionario();
    $total_funcionarios = $crudFuncionario->count();
    $funcionarios_ativos = $crudFuncionario->countActive();
    echo "Funcionários: Total = $total_funcionarios, Ativos = $funcionarios_ativos\n";
    
    $crudUsuario = new Crud_usuario();
    $total_usuarios = $crudUsuario->count();
    echo "Usuários: Total = $total_usuarios\n";
    
    $crudProduto = new Crud_produto();
    $total_produtos = $crudProduto->count();
    echo "Produtos: Total = $total_produtos\n";
    
    $crudPedido = new Crud_pedido();
    $total_pedidos = $crudPedido->count();
    echo "Pedidos: Total = $total_pedidos\n";
    
    $crudCategoria = new Crud_categoria();
    $total_categorias = $crudCategoria->count();
    echo "Categorias: Total = $total_categorias\n";
    
    echo "\nTodos os testes passaram com sucesso!\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
?>

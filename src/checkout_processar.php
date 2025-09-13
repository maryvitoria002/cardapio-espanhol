<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

// Verifica se há itens no carrinho
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    $_SESSION['erro_checkout'] = "Seu carrinho está vazio!";
    header("Location: ./carrinho.php");
    exit();
}

try {
    require_once "./controllers/pedido/Crud_pedido.php";
    require_once "./controllers/produto_pedido/Crud_produto_pedido.php";
    require_once "./controllers/produto/Crud_produto.php";
    
    $crudPedido = new Crud_pedido();
    $crudProdutoPedido = new Crud_produto_pedido();
    $crudProduto = new Crud_produto();
    
    // Calcular total do pedido
    $totalPedido = 0;
    $itensCarrinho = [];
    
    foreach ($_SESSION['carrinho'] as $item) {
        // Verificar se o item tem a estrutura correta
        if (!is_array($item) || !isset($item['id_produto'], $item['quantidade'], $item['preco'])) {
            continue;
        }
        
        // Verificar se o produto ainda existe na base de dados
        $produto = $crudProduto->readById($item['id_produto']);
        if ($produto) {
            $subtotal = $produto['preco'] * $item['quantidade'];
            $totalPedido += $subtotal;
            
            $itensCarrinho[] = [
                'id_produto' => $item['id_produto'],
                'quantidade' => $item['quantidade'],
                'preco' => $produto['preco'], // Usar preço atual da base de dados
                'nome' => $produto['nome_produto']
            ];
        }
    }
    
    if (empty($itensCarrinho)) {
        throw new Exception("Nenhum produto válido encontrado no carrinho!");
    }
    
    // Criar o pedido
    $crudPedido->setId_usuario($_SESSION['id']);
    $crudPedido->setStatus_pedido('Pendente');
    $crudPedido->setStatus_pagamento('Pendente');
    $crudPedido->setModo_pagamento('Dinheiro');  // Padrão
    $crudPedido->setEndereco('A definir');        // Padrão
    $crudPedido->setNota('Pedido realizado online');  // Nota padrão
    
    $idPedidoCriado = $crudPedido->create();
    
    if (!$idPedidoCriado) {
        throw new Exception("Erro ao criar pedido!");
    }
    
    // Adicionar itens do pedido
    foreach ($itensCarrinho as $item) {
        $crudProdutoPedido->setId_pedido($idPedidoCriado);
        $crudProdutoPedido->setId_produto($item['id_produto']);
        $crudProdutoPedido->setQuantidade($item['quantidade']);
        $crudProdutoPedido->setPreco($item['preco']);
        
        $resultado = $crudProdutoPedido->create();
        
        if (!$resultado) {
            throw new Exception("Erro ao adicionar item: " . $item['nome']);
        }
    }
    
    // Limpar carrinho após sucesso
    unset($_SESSION['carrinho']);
    
    // Redirecionar para histórico com mensagem de sucesso
    $_SESSION['sucesso_checkout'] = "Pedido #$idPedidoCriado realizado com sucesso! Total: R$ " . number_format($totalPedido, 2, ',', '.');
    header("Location: ./historico.php");
    exit();
    
} catch (Exception $e) {
    $_SESSION['erro_checkout'] = "Erro ao processar pedido: " . $e->getMessage();
    header("Location: ./carrinho.php");
    exit();
}
?>

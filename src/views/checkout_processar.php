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

// Verifica se os dados do formulário foram enviados
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ./checkout.php");
    exit();
}

// Processar dados do formulário
$endereco_entrega = '';
$telefone_contato = trim($_POST['telefone_contato'] ?? '');
$modo_pagamento = trim($_POST['modo_pagamento'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');
$referencia = trim($_POST['referencia'] ?? '');

// Determinar endereço a ser usado
if (isset($_POST['endereco_entrega']) && !empty(trim($_POST['endereco_entrega']))) {
    // Usar endereço preenchido no formulário
    $endereco_entrega = trim($_POST['endereco_entrega']);
} elseif (isset($_POST['endereco_salvo']) && !empty(trim($_POST['endereco_salvo']))) {
    // Usar endereço salvo do usuário
    $endereco_entrega = trim($_POST['endereco_salvo']);
} else {
    $_SESSION['erro_checkout'] = "Endereço de entrega é obrigatório!";
    header("Location: ./checkout.php");
    exit();
}

// Adicionar referência ao endereço se fornecida
if (!empty($referencia)) {
    $endereco_entrega .= " (Ref: " . $referencia . ")";
}

// Validar campos obrigatórios
if (empty($telefone_contato)) {
    $_SESSION['erro_checkout'] = "Telefone de contato é obrigatório!";
    header("Location: ./checkout.php");
    exit();
}

if (empty($modo_pagamento)) {
    $_SESSION['erro_checkout'] = "Forma de pagamento é obrigatória!";
    header("Location: ./checkout.php");
    exit();
}

try {
    require_once "../models/Crud_pedido.php";
    require_once "../models/Crud_produto_pedido.php";
    require_once "../models/Crud_produto.php";
    
    $crudPedido = new Crud_pedido();
    $crudProdutoPedido = new Crud_produto_pedido();
    $crudProduto = new Crud_produto();
    
    // Calcular total do pedido
    $totalPedido = 0;
    $frete = 2.00; // Taxa de entrega fixa
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
    
    // Adicionar frete ao total
    $totalPedido += $frete;
    
    if (empty($itensCarrinho)) {
        throw new Exception("Nenhum produto válido encontrado no carrinho!");
    }
    
    // Criar o pedido com dados do formulário
    $crudPedido->setId_usuario($_SESSION['id']);
    $crudPedido->setStatus_pedido('Pendente');
    $crudPedido->setStatus_pagamento('Pendente');
    $crudPedido->setModo_pagamento($modo_pagamento);
    $crudPedido->setEndereco($endereco_entrega);
    
    // Criar nota com observações e telefone
    $nota_pedido = "Pedido realizado online";
    if (!empty($telefone_contato)) {
        $nota_pedido .= " | Tel: " . $telefone_contato;
    }
    if (!empty($observacoes)) {
        $nota_pedido .= " | Obs: " . $observacoes;
    }
    $crudPedido->setNota($nota_pedido);
    
    $idPedidoCriado = $crudPedido->create();
    
    if (!$idPedidoCriado) {
        throw new Exception("Erro ao criar pedido!");
    }
    
    // Verificar e diminuir estoque antes de adicionar itens do pedido
    foreach ($itensCarrinho as $item) {
        try {
            $crudProduto->diminuirEstoque($item['id_produto'], $item['quantidade']);
        } catch (Exception $e) {
            // Se houver erro de estoque, cancelar o pedido criado
            $crudPedido->setId_pedido($idPedidoCriado);
            $crudPedido->delete($idPedidoCriado);
            throw new Exception("Erro de estoque para " . $item['nome'] . ": " . $e->getMessage());
        }
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

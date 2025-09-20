<?php 
$titulo = "carrinho";
include_once "./components/_base-header.php";
require_once "./controllers/produto/Crud_produto.php";
require_once "./controllers/carrinho/Carrinho.php";
?>

<link rel="stylesheet" href="./styles/carrinho.css">

<?php
// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Instanciar controlador de produtos
$produtoController = new Crud_produto();

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Adicionar produto ao carrinho
    if (isset($_POST['add_to_cart']) || (isset($_POST['acao']) && $_POST['acao'] === 'adicionar')) {
        $produto_id = (int)($_POST['produto_id'] ?? $_POST['id_produto']);
        $quantidade = (int)$_POST['quantidade'];
        
        if ($produto_id > 0 && $quantidade > 0) {
            $dadosProduto = $produtoController->readById($produto_id);
            
            if ($dadosProduto) {
                $item_carrinho = [
                    'id_produto' => $produto_id,
                    'nome' => $dadosProduto['nome_produto'],
                    'preco' => $dadosProduto['preco'],
                    'quantidade' => $quantidade,
                    'imagem' => $dadosProduto['imagem']
                ];
                
                // Verificar se o produto já está no carrinho
                $found = false;
                foreach ($_SESSION['carrinho'] as &$item_existente) {
                    if ($item_existente['id_produto'] == $produto_id) {
                        $item_existente['quantidade'] += $quantidade;
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $_SESSION['carrinho'][] = $item_carrinho;
                }
                
                $_SESSION['mensagem_sucesso'] = "¡Producto agregado al carrito con éxito!";
            } else {
                $_SESSION['mensagem_erro'] = "Producto no encontrado.";
            }
        } else {
            $_SESSION['mensagem_erro'] = "Datos inválidos para agregar al carrito.";
        }
    }
    
    // Atualizar quantidade
    if (isset($_POST['update_quantity'])) {
        $produto_id = (int)$_POST['produto_id'];
        $nova_quantidade = (int)$_POST['nova_quantidade'];
        
        if ($nova_quantidade > 0) {
            foreach ($_SESSION['carrinho'] as &$item) {
                if ($item['id_produto'] == $produto_id) {
                    $item['quantidade'] = $nova_quantidade;
                    break;
                }
            }
        } else {
            foreach ($_SESSION['carrinho'] as $key => $item) {
                if ($item['id_produto'] == $produto_id) {
                    unset($_SESSION['carrinho'][$key]);
                    break;
                }
            }
            $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
        }
    }
    
    // Remover item
    if (isset($_POST['remove_item'])) {
        $produto_id = (int)$_POST['produto_id'];
        
        foreach ($_SESSION['carrinho'] as $key => $item) {
            if ($item['id_produto'] == $produto_id) {
                unset($_SESSION['carrinho'][$key]);
                break;
            }
        }
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
    }
    
    // Limpar carrinho
    if (isset($_POST['clear_cart'])) {
        $_SESSION['carrinho'] = [];
    }
    
    header("Location: ./carrinho.php");
    exit();
}

// Calcular totais
$total_itens = 0;
$subtotal = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $total_itens += $item['quantidade'];
    $subtotal += $item['preco'] * $item['quantidade'];
}

$taxa_entrega = $subtotal > 0 ? 2.00 : 0;
$total = $subtotal + $taxa_entrega;

// Processar mensagens
$mensagem_sucesso = '';
$mensagem_erro = '';

if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem_sucesso = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

if (isset($_SESSION['mensagem_erro'])) {
    $mensagem_erro = $_SESSION['mensagem_erro'];
    unset($_SESSION['mensagem_erro']);
}
?>

<div class="carrinho-container">
    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($mensagem_sucesso) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($mensagem_erro): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($mensagem_erro) ?>
        </div>
    <?php endif; ?>
    
    <div class="carrinho-header">
        <h1>Tu Carrito</h1>
        <p><?= $total_itens ?> artículo<?= $total_itens != 1 ? 's' : '' ?> en el carrito</p>
    </div>

    <div class="carrinho-content">
        <div class="carrinho-items">
            <?php if (empty($_SESSION['carrinho'])): ?>
                <div class="carrinho-vazio">
                    <h2>Tu carrito está vacío</h2>
                    <p>¡Agrega algunos platos deliciosos de nuestro menú!</p>
                    <a href="./cardapio.php" class="btn-voltar-cardapio">Ver Menú</a>
                </div>
            <?php else: ?>
                <div class="items-list">
                    <?php foreach ($_SESSION['carrinho'] as $item): ?>
                        <div class="carrinho-item">
                            <div class="item-info">
                                <h3><?= htmlspecialchars($item['nome']) ?></h3>
                                <p>R$ <?= number_format($item['preco'], 2, ',', '.') ?></p>
                                <p>Cantidad: <?= $item['quantidade'] ?></p>
                                <p>Subtotal: R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></p>
                            </div>
                            <div class="item-acoes">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="produto_id" value="<?= $item['id_produto'] ?>">
                                    <button type="submit" name="remove_item">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="carrinho-resumo">
                    <h3>Resumen del Pedido</h3>
                    <p>Subtotal: R$ <?= number_format($subtotal, 2, ',', '.') ?></p>
                    <p>Entrega: R$ <?= number_format($taxa_entrega, 2, ',', '.') ?></p>
                    <p><strong>Total: R$ <?= number_format($total, 2, ',', '.') ?></strong></p>
                    
                    <button onclick="window.location.href='./checkout.php'" class="btn-finalizar">
                        Finalizar Orden
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
include_once "./components/_base-footer.php";
?>
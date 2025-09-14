<?php 
$titulo = "carrinho";
include_once "./components/_base-header.php";
require_once "./controllers/produto/Crud_produto.php";

if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

// Exibir mensagens de erro ou sucesso
$mensagem_erro = '';
$mensagem_sucesso = '';

if (isset($_SESSION['erro_checkout'])) {
    $mensagem_erro = $_SESSION['erro_checkout'];
    unset($_SESSION['erro_checkout']);
}

if (isset($_SESSION['sucesso_checkout'])) {
    $mensagem_sucesso = $_SESSION['sucesso_checkout'];
    unset($_SESSION['sucesso_checkout']);
}

// Adicionar mensagens para ações do carrinho
if (isset($_SESSION['sucesso_carrinho'])) {
    $mensagem_sucesso = $_SESSION['sucesso_carrinho'];
    unset($_SESSION['sucesso_carrinho']);
}

if (isset($_SESSION['erro_carrinho'])) {
    $mensagem_erro = $_SESSION['erro_carrinho'];
    unset($_SESSION['erro_carrinho']);
}

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Limpar itens inválidos do carrinho
$carrinho_limpo = [];
foreach ($_SESSION['carrinho'] as $item) {
    if (is_array($item) && isset($item['id_produto'], $item['nome'], $item['preco'], $item['quantidade'])) {
        $carrinho_limpo[] = $item;
    }
}
$_SESSION['carrinho'] = $carrinho_limpo;

// Instanciar controlador de produtos
$produtoController = new Crud_produto();

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Adicionar produto ao carrinho (suporte para ambas as formas: add_to_cart e acao=adicionar)
    if (isset($_POST['add_to_cart']) || (isset($_POST['acao']) && $_POST['acao'] === 'adicionar')) {
        $produto_id = (int)($_POST['produto_id'] ?? $_POST['id_produto']);
        $quantidade = (int)($_POST['quantidade'] ?? 1);
        
        if ($produto_id > 0 && $quantidade > 0) {
            // Buscar dados do produto usando readById para garantir que funcione
            $dadosProduto = $produtoController->readById($produto_id);
            
            if ($dadosProduto) {
                $item_carrinho = [
                    'id_produto' => $produto_id,
                    'nome' => $dadosProduto['nome_produto'],
                    'preco' => $dadosProduto['preco'],
                    'quantidade' => $quantidade,
                    'imagem' => $dadosProduto['imagem'],
                    'categoria' => $dadosProduto['categoria'] ?? ''
                ];
                
                // Verificar se o produto já está no carrinho
                $found = false;
                foreach ($_SESSION['carrinho'] as &$item) {
                    if ($item['id_produto'] == $produto_id) {
                        $item['quantidade'] += $quantidade;
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $_SESSION['carrinho'][] = $item_carrinho;
                }
                
                // Adicionar mensagem de sucesso
                $_SESSION['sucesso_carrinho'] = "Produto adicionado ao carrinho com sucesso!";
            } else {
                // Produto não encontrado
                $_SESSION['erro_carrinho'] = "Produto não encontrado.";
            }
        } else {
            // Dados inválidos
            $_SESSION['erro_carrinho'] = "Dados inválidos para adicionar ao carrinho.";
        }
    }
    
    // Atualizar quantidade
    if (isset($_POST['update_quantity'])) {
        $produto_id = (int)$_POST['produto_id'];
        $nova_quantidade = (int)$_POST['nova_quantidade'];
        
        foreach ($_SESSION['carrinho'] as $key => &$item) {
            if ($item['id_produto'] == $produto_id) {
                if ($nova_quantidade <= 0) {
                    // Remove item se quantidade for 0 ou menor
                    unset($_SESSION['carrinho'][$key]);
                } else {
                    $item['quantidade'] = $nova_quantidade;
                }
                break;
            }
        }
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // Reindexar array
    }
    
    // Remover produto
    if (isset($_POST['remove_item'])) {
        $produto_id = (int)$_POST['produto_id'];
        foreach ($_SESSION['carrinho'] as $key => $item) {
            if ($item['id_produto'] == $produto_id) {
                unset($_SESSION['carrinho'][$key]);
                break;
            }
        }
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // Reindexar array
    }
    
    // Limpar carrinho
    if (isset($_POST['clear_cart'])) {
        $_SESSION['carrinho'] = [];
    }
    
    // Verificar se a requisição é AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    // Se for AJAX, retornar resposta JSON
    if ($isAjax) {
        header('Content-Type: application/json');
        
        if (isset($_SESSION['sucesso_carrinho'])) {
            echo json_encode(['success' => true, 'message' => $_SESSION['sucesso_carrinho']]);
            unset($_SESSION['sucesso_carrinho']);
        } elseif (isset($_SESSION['erro_carrinho'])) {
            echo json_encode(['success' => false, 'message' => $_SESSION['erro_carrinho']]);
            unset($_SESSION['erro_carrinho']);
        } else {
            echo json_encode(['success' => true]);
        }
        exit();
    }
    
    // Se não for AJAX, redirecionar normalmente
    header("Location: ./carrinho.php");
    exit();
}

// Calcular totais
$total_itens = 0;
$subtotal = 0;
foreach ($_SESSION['carrinho'] as $item) {
    // Verificar se o item é um array válido
    if (is_array($item) && isset($item['quantidade']) && isset($item['preco'])) {
        $total_itens += $item['quantidade'];
        $subtotal += $item['preco'] * $item['quantidade'];
    }
}

$taxa_entrega = $subtotal > 0 ? 2.00 : 0; // Taxa de entrega fixa
$desconto = 0; // Aqui pode implementar lógica de desconto
$total = $subtotal + $taxa_entrega - $desconto;
?>

<div class="carrinho-container">
    <!-- Mensagens de erro e sucesso -->
    <?php if ($mensagem_erro): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($mensagem_erro) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($mensagem_sucesso) ?>
        </div>
    <?php endif; ?>
    
    <div class="carrinho-header">
        <div class="breadcrumb">
            <a href="./cardapio.php"><i class="fas fa-utensils"></i> Cardápio</a>
            <span>/</span>
            <span class="current">Carrinho</span>
        </div>
        <h1>Seu Carrinho</h1>
        <p><?= $total_itens ?> item<?= $total_itens != 1 ? 's' : '' ?> no carrinho</p>
    </div>

    <div class="carrinho-content">
        <div class="carrinho-items">
            <?php if (empty($_SESSION['carrinho'])): ?>
                <!-- Carrinho vazio -->
                <div class="carrinho-vazio">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Seu carrinho está vazio</h2>
                    <p>Adicione alguns pratos deliciosos do nosso cardápio!</p>
                    <a href="./cardapio.php" class="btn-voltar-cardapio">
                        <i class="fas fa-utensils"></i>
                        Ver Cardápio
                    </a>
                </div>
            <?php else: ?>
                <!-- Header da lista -->
                <div class="items-header">
                    <h2>Itens do Pedido</h2>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="clear_cart" class="btn-limpar-carrinho" 
                                onclick="return confirm('Tem certeza que deseja limpar o carrinho?')">
                            <i class="fas fa-trash"></i>
                            Limpar Carrinho
                        </button>
                    </form>
                </div>

                <!-- Lista de itens -->
                <div class="items-list">
                    <?php foreach ($_SESSION['carrinho'] as $item): ?>
                        <?php if (is_array($item) && isset($item['id_produto'], $item['nome'], $item['preco'], $item['quantidade'])): ?>
                        <div class="carrinho-item">
                            <div class="item-imagem">
                                <?php 
                                $imagemPath = "./images/comidas/" . (isset($item['categoria']) && $item['categoria'] ? str_replace(' ', '_', $item['categoria']) . '/' : '') . ($item['imagem'] ?? '');
                                if (!empty($item['imagem']) && file_exists($imagemPath)): ?>
                                    <img src="<?= htmlspecialchars($imagemPath) ?>" 
                                         alt="<?= htmlspecialchars($item['nome']) ?>">
                                <?php else: ?>
                                    <img src="./assets/cardapio.png" 
                                         alt="<?= htmlspecialchars($item['nome']) ?>">
                                <?php endif; ?>
                            </div>

                            <div class="item-info">
                                <h3 class="item-nome"><?= htmlspecialchars($item['nome']) ?></h3>
                                <p class="item-preco-unitario">R$ <?= number_format($item['preco'], 2, ',', '.') ?> cada</p>
                            </div>

                            <div class="item-quantidade">
                                <form method="POST" class="quantidade-form">
                                    <input type="hidden" name="produto_id" value="<?= $item['id_produto'] ?>">
                                    <input type="hidden" name="update_quantity" value="1">
                                    
                                    <button type="submit" name="nova_quantidade" value="<?= $item['quantidade'] - 1 ?>" 
                                            class="qty-btn minus" <?= $item['quantidade'] <= 1 ? 'disabled' : '' ?>>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    
                                    <span class="qty-display"><?= $item['quantidade'] ?></span>
                                    
                                    <button type="submit" name="nova_quantidade" value="<?= $item['quantidade'] + 1 ?>" 
                                            class="qty-btn plus">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="item-subtotal">
                                <span class="preco-total">R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
                            </div>

                            <div class="item-acoes">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="produto_id" value="<?= $item['id_produto'] ?>">
                                    <button type="submit" name="remove_item" class="btn-remover" 
                                            onclick="return confirm('Remover este item do carrinho?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Botão continuar comprando -->
                <div class="continuar-comprando">
                    <a href="./cardapio.php" class="btn-continuar">
                        <i class="fas fa-plus"></i>
                        Adicionar mais itens
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['carrinho'])): ?>
            <!-- Resumo do pedido -->
            <div class="carrinho-resumo">
                <div class="resumo-card">
                    <h3>Resumo do Pedido</h3>
                    
                    <div class="resumo-linha">
                        <span>Subtotal (<?= $total_itens ?> item<?= $total_itens != 1 ? 's' : '' ?>)</span>
                        <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                    </div>
                    
                    <div class="resumo-linha">
                        <span>Taxa de entrega</span>
                        <span>R$ <?= number_format($taxa_entrega, 2, ',', '.') ?></span>
                    </div>
                    
                    <?php if ($desconto > 0): ?>
                        <div class="resumo-linha desconto">
                            <span>Desconto</span>
                            <span>-R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="resumo-linha total">
                        <strong>
                            <span>Total</span>
                            <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </strong>
                    </div>
                    
                    <!-- Cupom de desconto -->
                    <div class="cupom-desconto">
                        <h4>Cupom de Desconto</h4>
                        <form class="cupom-form">
                            <input type="text" placeholder="Digite seu cupom" class="cupom-input">
                            <button type="submit" class="btn-aplicar-cupom">Aplicar</button>
                        </form>
                    </div>
                    
                    <!-- Botão finalizar pedido -->
                    <div class="finalizar-pedido">
                        <button class="btn-finalizar" onclick="finalizarPedido()">
                            <i class="fas fa-credit-card"></i>
                            Finalizar Pedido
                            <span class="preco-btn">R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </button>
                    </div>
                    
                    <!-- Métodos de pagamento -->
                    <div class="metodos-pagamento">
                        <h4>Aceitamos</h4>
                        <div class="pagamento-icons">
                            <i class="fas fa-credit-card" title="Cartão de Crédito"></i>
                            <i class="fas fa-money-bill-wave" title="Dinheiro"></i>
                            <i class="fab fa-pix" title="PIX"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Função para finalizar pedido
function finalizarPedido() {
    // Verificar se há itens no carrinho
    const itensCarrinho = document.querySelectorAll('.carrinho-item');
    if (itensCarrinho.length === 0) {
        alert('Seu carrinho está vazio!');
        return;
    }
    
    // Redirecionar para página de checkout em vez de processar diretamente
    window.location.href = './checkout.php';
}

// Confirmar antes de limpar carrinho
function confirmarLimparCarrinho() {
    return confirm('Tem certeza que deseja limpar todo o carrinho?');
}

// Atualizar quantidade com delay para evitar muitos submits
let timeoutId;
function atualizarQuantidadeComDelay(form) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        form.submit();
    }, 500);
}
</script>

<style>
/* Alertas */
.alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert i {
    font-size: 1.1rem;
}
</style>

<?php 
include_once "./components/_base-footer.php";
?>

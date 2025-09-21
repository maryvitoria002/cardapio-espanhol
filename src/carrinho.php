<?php 
session_start();
require_once "./controllers/produto/Crud_produto.php";

// Verificar se o usuário está logado ANTES de incluir o header
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
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

// Processar ações do carrinho ANTES de incluir o header
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
                $_SESSION['sucesso_carrinho'] = "¡Producto agregado al carrito con éxito!";
            } else {
                // Produto não encontrado
                $_SESSION['erro_carrinho'] = "Producto no encontrado.";
            }
        } else {
            // Dados inválidos
            $_SESSION['erro_carrinho'] = "Datos inválidos para agregar al carrito.";
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
    
    // Aplicar cupom de desconto
    if (isset($_POST['aplicar_cupom'])) {
        $cupom = trim($_POST['codigo_cupom'] ?? '');
        
        // Lista de cupons válidos
        $cupons_validos = [
            'Samyllefrete' => [
                'desconto' => 2.00,
                'tipo' => 'fixo', // fixo ou percentual
                'nome' => 'Desconto Samylle Frete'
            ],
            'Brunolindo' => [
                'desconto' => 20.00,
                'tipo' => 'percentual',
                'nome' => 'Desconto Bruno Lindo'
            ]
            // Pode adicionar mais cupons aqui
        ];
        
        // Buscar cupom independente de maiúsculas/minúsculas
        $cupom_encontrado = null;
        $codigo_original = null;
        foreach ($cupons_validos as $codigo => $dados) {
            if (strtolower($codigo) === strtolower($cupom)) {
                $cupom_encontrado = $dados;
                $codigo_original = $codigo;
                break;
            }
        }
        
        if ($cupom_encontrado !== null) {
            $_SESSION['cupom_aplicado'] = [
                'codigo' => $codigo_original,
                'desconto' => $cupom_encontrado['desconto'],
                'tipo' => $cupom_encontrado['tipo'],
                'nome' => $cupom_encontrado['nome']
            ];
            
            // Mensagem personalizada baseada no tipo de desconto
            if ($cupom_encontrado['tipo'] === 'percentual') {
                $_SESSION['sucesso_carrinho'] = "¡Cupón '{$codigo_original}' aplicado con éxito! Descuento del " . number_format($cupom_encontrado['desconto'], 0) . "%";
            } else {
                $_SESSION['sucesso_carrinho'] = "¡Cupón '{$codigo_original}' aplicado con éxito! Descuento de R$ " . number_format($cupom_encontrado['desconto'], 2, ',', '.');
            }
        } else {
            $_SESSION['erro_carrinho'] = "Cupón inválido o expirado.";
        }
    }
    
    // Remover cupom
    if (isset($_POST['remover_cupom'])) {
        unset($_SESSION['cupom_aplicado']);
        $_SESSION['sucesso_carrinho'] = "¡Cupón removido con éxito!";
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
    
    // Se não for AJAX, redirecionar apenas para algumas ações específicas
    if (isset($_POST['add_to_cart']) || (isset($_POST['acao']) && $_POST['acao'] === 'adicionar')) {
        header("Location: ./carrinho.php");
        exit();
    }
}

$titulo = "carrinho";
include_once "./components/_base-header.php";
?>

<link rel="stylesheet" href="./styles/carrinho.css">

<?php
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

// Calcular desconto do cupom
$desconto = 0;
$cupom_aplicado = null;
if (isset($_SESSION['cupom_aplicado'])) {
    $cupom_aplicado = $_SESSION['cupom_aplicado'];
    if ($cupom_aplicado['tipo'] === 'fixo') {
        $desconto = $cupom_aplicado['desconto'];
    } elseif ($cupom_aplicado['tipo'] === 'percentual') {
        $desconto = ($subtotal * $cupom_aplicado['desconto']) / 100;
    }
}

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
            <a href="./cardapio.php"><i class="fas fa-utensils"></i> Menú</a>
            <span>/</span>
            <span class="current">Carrito</span>
        </div>
        <h1>Tu Carrito</h1>
        <p><?= $total_itens ?> artículo<?= $total_itens != 1 ? 's' : '' ?> en el carrito</p>
    </div>

    <div class="carrinho-content">
        <div class="carrinho-items">
            <?php if (empty($_SESSION['carrinho'])): ?>
                <!-- Carrinho vazio -->
                <div class="carrinho-vazio">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Tu carrito está vacío</h2>
                    <p>¡Agrega algunos platos deliciosos de nuestro menú!</p>
                    <a href="./cardapio.php" class="btn-voltar-cardapio">
                        <i class="fas fa-utensils"></i>
                        Ver Menú
                    </a>
                </div>
            <?php else: ?>
                <!-- Header da lista -->
                <div class="items-header">
                    <h2>Artículos del Pedido</h2>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="clear_cart" class="btn-limpar-carrinho" 
                                onclick="return confirm('¿Estás seguro de que quieres vaciar el carrito?')">
                            <i class="fas fa-trash"></i>
                            Vaciar Carrito
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
                                <p class="item-preco-unitario">R$ <?= number_format($item['preco'], 2, ',', '.') ?> cada uno</p>
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
                                            onclick="return confirm('¿Remover este artículo del carrito?')">
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
                        Agregar más artículos
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['carrinho'])): ?>
            <!-- Resumo do pedido -->
            <div class="carrinho-resumo">
                <div class="resumo-card">
                    <h3>Resumen del Pedido</h3>
                    
                    <div class="resumo-linha">
                        <span>Subtotal (<?= $total_itens ?> artículo<?= $total_itens != 1 ? 's' : '' ?>)</span>
                        <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                    </div>
                    
                    <div class="resumo-linha">
                        <span>Tarifa de entrega</span>
                        <span>R$ <?= number_format($taxa_entrega, 2, ',', '.') ?></span>
                    </div>
                    
                    <?php if ($desconto > 0 && $cupom_aplicado): ?>
                        <div class="resumo-linha desconto">
                            <?php if ($cupom_aplicado['tipo'] === 'percentual'): ?>
                                <span>Descuento (<?= htmlspecialchars($cupom_aplicado['codigo']) ?> - <?= number_format($cupom_aplicado['desconto'], 0) ?>%)</span>
                            <?php else: ?>
                                <span>Descuento (<?= htmlspecialchars($cupom_aplicado['codigo']) ?>)</span>
                            <?php endif; ?>
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
                        <h4>Cupón de descuento</h4>
                        
                        <?php if (isset($_SESSION['cupom_aplicado'])): ?>
                            <!-- Cupom aplicado -->
                            <div class="cupom-aplicado">
                                <div class="cupom-info">
                                    <i class="fas fa-check-circle"></i>
                                    <span><strong><?= htmlspecialchars($_SESSION['cupom_aplicado']['codigo']) ?></strong> - <?= htmlspecialchars($_SESSION['cupom_aplicado']['nome']) ?></span>
                                </div>
                                <form method="POST" style="display: inline;">
                                    <button type="submit" name="remover_cupom" class="btn-remover-cupom" title="Remover cupón">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <!-- Formulário para aplicar cupom -->
                            <form method="POST" class="cupom-form">
                                <input type="text" name="codigo_cupom" placeholder="Ingresa tu cupón (ej: Samyllefrete, Brunolindo)" class="cupom-input" required>
                                <button type="submit" name="aplicar_cupom" class="btn-aplicar-cupom">Aplicar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Botão finalizar pedido -->
                    <div class="finalizar-pedido">
                        <button class="btn-finalizar" onclick="finalizarPedido()">
                            <i class="fas fa-credit-card"></i>
                            Finalizar Compra
                            <span class="preco-btn">R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </button>
                    </div>
                    
                    <!-- Métodos de pagamento -->
                    <div class="metodos-pagamento">
                        <h4>Aceptamos</h4>
                        <div class="pagamento-icons">
                            <i class="fas fa-credit-card" title="Tarjeta de Crédito"></i>
                            <i class="fas fa-money-bill-wave" title="Efectivo"></i>
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
        alert('¡Tu carrito está vacío!');
        return;
    }
    
    // Redirecionar para página de checkout em vez de processar diretamente
    window.location.href = './checkout.php';
}

// Confirmar antes de limpar carrinho
function confirmarLimparCarrinho() {
    return confirm('¿Estás seguro de que quieres vaciar todo el carrito?');
}

// Atualizar quantidade com delay para evitar muitos submits
let timeoutId;
function atualizarQuantidadeComDelay(form) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        form.submit();
    }, 500);
}

// Detectar scroll no carrinho e adicionar indicador visual
document.addEventListener('DOMContentLoaded', function() {
    const carrinhoItems = document.querySelector('.carrinho-items');
    
    if (carrinhoItems) {
        function checkScroll() {
            const hasScroll = carrinhoItems.scrollHeight > carrinhoItems.clientHeight;
            const isScrolledToBottom = carrinhoItems.scrollTop + carrinhoItems.clientHeight >= carrinhoItems.scrollHeight - 5;
            
            if (hasScroll && !isScrolledToBottom) {
                carrinhoItems.classList.add('has-scroll');
            } else {
                carrinhoItems.classList.remove('has-scroll');
            }
        }
        
        // Verificar inicialmente
        checkScroll();
        
        // Verificar ao rolar
        carrinhoItems.addEventListener('scroll', checkScroll);
        
        // Verificar quando o conteúdo muda (após adicionar/remover itens)
        window.addEventListener('resize', checkScroll);
    }
    
    // Funcionalidade do cupom
    const cupomForm = document.querySelector('.cupom-form');
    if (cupomForm) {
        const cupomInput = cupomForm.querySelector('input[name="codigo_cupom"]');
        
        // Converter para maiúsculas conforme o usuário digita
        if (cupomInput) {
            cupomInput.addEventListener('input', function(e) {
                // Remover apenas caracteres especiais, mantendo letras e números
                e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '');
            });
        }
        
        // Feedback visual ao aplicar cupom
        cupomForm.addEventListener('submit', function(e) {
            const submitBtn = cupomForm.querySelector('.btn-aplicar-cupom');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Aplicando...';
            submitBtn.disabled = true;
            
            // Restaurar após um delay caso algo dê errado
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    }
});
</script>

<?php 
include_once "./components/_base-footer.php";
?>
<?php
session_start();

// Incluir arquivos necessários
require_once __DIR__ . '/db/conection.php';
require_once __DIR__ . '/controllers/produto/Crud_produto.php';

// Verificar se usuário está logado
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
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
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $id_produto = (int)($_POST['id_produto'] ?? 0);
            $quantidade = (int)($_POST['quantidade'] ?? 1);
            
            if ($id_produto > 0 && $quantidade > 0) {
                if (isset($_SESSION['carrinho'][$id_produto])) {
                    $_SESSION['carrinho'][$id_produto] += $quantidade;
                } else {
                    $_SESSION['carrinho'][$id_produto] = $quantidade;
                }
                
                // Redirect para evitar resubmissão
                header('Location: carrinho_novo.php');
                exit();
            }
            break;
            
        case 'update':
            $id_produto = (int)($_POST['id_produto'] ?? 0);
            $quantidade = (int)($_POST['quantidade'] ?? 0);
            
            if ($id_produto > 0) {
                if ($quantidade > 0) {
                    $_SESSION['carrinho'][$id_produto] = $quantidade;
                } else {
                    unset($_SESSION['carrinho'][$id_produto]);
                }
                
                header('Location: carrinho_novo.php');
                exit();
            }
            break;
            
        case 'remove':
            $id_produto = (int)($_POST['id_produto'] ?? 0);
            
            if ($id_produto > 0 && isset($_SESSION['carrinho'][$id_produto])) {
                unset($_SESSION['carrinho'][$id_produto]);
                
                header('Location: carrinho_novo.php');
                exit();
            }
            break;
            
        case 'clear':
            $_SESSION['carrinho'] = [];
            header('Location: carrinho_novo.php');
            exit();
            break;
    }
}

// Buscar dados dos produtos no carrinho
$itensCarrinho = [];
$subtotal = 0;

if (!empty($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $id_produto => $quantidade) {
        $produto = $produtoController->read($id_produto);
        if ($produto) {
            $produto['quantidade'] = $quantidade;
            $produto['subtotal'] = $produto['preco'] * $quantidade;
            $itensCarrinho[] = $produto;
            $subtotal += $produto['subtotal'];
        }
    }
}

// Calcular totais
$taxaEntrega = 8.90;
$desconto = 0;
$total = $subtotal + $taxaEntrega - $desconto;

// Dados do usuário
$nomeUsuario = $_SESSION['nome'] ?? 'Usuário';
$emailUsuario = $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras - Écoute Saveur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="styles/carrinho.css" rel="stylesheet">
</head>
<body>
    <?php include_once 'components/_base-header.php'; ?>
    
    <div class="carrinho-container">
        <!-- Header do carrinho -->
        <div class="carrinho-header">
            <nav class="breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Início</a>
                <i class="fas fa-chevron-right"></i>
                <a href="cardapio.php">Cardápio</a>
                <i class="fas fa-chevron-right"></i>
                <span class="current">Carrinho</span>
            </nav>
            
            <h1><i class="fas fa-shopping-cart"></i> Seu Carrinho</h1>
            <p>Revise seus itens e finalize seu pedido</p>
        </div>

        <!-- Conteúdo do carrinho -->
        <div class="carrinho-content">
            <!-- Seção dos itens -->
            <div class="carrinho-items">
                <?php if (empty($itensCarrinho)): ?>
                    <!-- Carrinho vazio -->
                    <div class="carrinho-vazio">
                        <div class="empty-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h2>Seu carrinho está vazio</h2>
                        <p>Adicione alguns deliciosos pratos do nosso cardápio!</p>
                        <a href="cardapio.php" class="btn-voltar-cardapio">
                            <i class="fas fa-utensils"></i>
                            Ver Cardápio
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Header dos itens -->
                    <div class="items-header">
                        <h2>Itens no Carrinho (<?= count($itensCarrinho) ?>)</h2>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn-limpar-carrinho" onclick="return confirm('Deseja limpar todo o carrinho?')">
                                <i class="fas fa-trash"></i>
                                Limpar Carrinho
                            </button>
                        </form>
                    </div>

                    <!-- Lista de itens -->
                    <div class="items-list">
                        <?php foreach ($itensCarrinho as $item): ?>
                            <div class="carrinho-item">
                                <!-- Imagem do item -->
                                <div class="item-imagem">
                                    <?php 
                                    $imagemPath = 'images/comidas/' . str_replace(' ', '_', $item['categoria']) . '/' . $item['imagem'];
                                    if (!file_exists($imagemPath)) {
                                        $imagemPath = 'assets/cardapio.png'; // Imagem padrão
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($imagemPath) ?>" alt="<?= htmlspecialchars($item['nome_produto']) ?>">
                                </div>

                                <!-- Informações do item -->
                                <div class="item-info">
                                    <h3 class="item-nome"><?= htmlspecialchars($item['nome_produto']) ?></h3>
                                    <p class="item-preco-unitario">R$ <?= number_format($item['preco'], 2, ',', '.') ?> por unidade</p>
                                </div>

                                <!-- Controles de quantidade -->
                                <div class="item-quantidade">
                                    <div class="quantidade-form">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_produto" value="<?= $item['id_produto'] ?>">
                                            <input type="hidden" name="quantidade" value="<?= max(1, $item['quantidade'] - 1) ?>">
                                            <button type="submit" class="qty-btn minus" <?= $item['quantidade'] <= 1 ? 'disabled' : '' ?>>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </form>
                                        
                                        <span class="qty-display"><?= $item['quantidade'] ?></span>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_produto" value="<?= $item['id_produto'] ?>">
                                            <input type="hidden" name="quantidade" value="<?= $item['quantidade'] + 1 ?>">
                                            <button type="submit" class="qty-btn plus">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Subtotal do item -->
                                <div class="item-subtotal">
                                    <div class="preco-total">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></div>
                                </div>

                                <!-- Ações do item -->
                                <div class="item-acoes">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="id_produto" value="<?= $item['id_produto'] ?>">
                                        <button type="submit" class="btn-remover" onclick="return confirm('Remover este item do carrinho?')" title="Remover item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Continuar comprando -->
                    <div class="continuar-comprando">
                        <a href="cardapio.php" class="btn-continuar">
                            <i class="fas fa-arrow-left"></i>
                            Continuar Comprando
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Resumo do carrinho -->
            <?php if (!empty($itensCarrinho)): ?>
                <div class="carrinho-resumo">
                    <div class="resumo-card">
                        <h3><i class="fas fa-receipt"></i> Resumo do Pedido</h3>
                        
                        <!-- Linhas do resumo -->
                        <div class="resumo-linha">
                            <span>Subtotal (<?= count($itensCarrinho) ?> itens):</span>
                            <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                        </div>
                        
                        <div class="resumo-linha">
                            <span>Taxa de entrega:</span>
                            <span>R$ <?= number_format($taxaEntrega, 2, ',', '.') ?></span>
                        </div>
                        
                        <?php if ($desconto > 0): ?>
                            <div class="resumo-linha desconto">
                                <span>Desconto:</span>
                                <span>-R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="resumo-linha total">
                            <strong>Total:</strong>
                            <strong>R$ <?= number_format($total, 2, ',', '.') ?></strong>
                        </div>

                        <!-- Cupom de desconto -->
                        <div class="cupom-desconto">
                            <h4><i class="fas fa-tag"></i> Cupom de Desconto</h4>
                            <form class="cupom-form" method="POST">
                                <input type="hidden" name="action" value="apply_coupon">
                                <input type="text" name="cupom" class="cupom-input" placeholder="Digite seu cupom" maxlength="20">
                                <button type="submit" class="btn-aplicar-cupom">Aplicar</button>
                            </form>
                        </div>

                        <!-- Finalizar pedido -->
                        <div class="finalizar-pedido">
                            <button type="button" class="btn-finalizar" onclick="finalizarPedido()">
                                <i class="fas fa-credit-card"></i>
                                <span>Finalizar Pedido</span>
                                <span class="preco-btn">R$ <?= number_format($total, 2, ',', '.') ?></span>
                            </button>
                        </div>

                        <!-- Métodos de pagamento -->
                        <div class="metodos-pagamento">
                            <h4>Formas de Pagamento Aceitas</h4>
                            <div class="pagamento-icons">
                                <i class="fab fa-cc-visa" title="Visa"></i>
                                <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                                <i class="fab fa-pix" title="PIX"></i>
                                <i class="fas fa-money-bill-wave" title="Dinheiro"></i>
                                <i class="fab fa-cc-paypal" title="PayPal"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once 'components/_base-footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Função para finalizar pedido
        function finalizarPedido() {
            // Verificar se há itens no carrinho
            <?php if (empty($itensCarrinho)): ?>
                alert('Seu carrinho está vazio! Adicione alguns itens antes de finalizar o pedido.');
                window.location.href = 'cardapio.php';
                return;
            <?php endif; ?>
            
            // Confirmar finalização
            if (confirm('Confirma a finalização do pedido no valor de R$ <?= number_format($total, 2, ',', '.') ?>?')) {
                // Aqui você pode redirecionar para a página de checkout/pagamento
                // ou implementar a lógica de finalização do pedido
                alert('Funcionalidade de pagamento será implementada em breve!');
                
                // Por enquanto, simular finalização e limpar carrinho
                // window.location.href = 'historico.php';
            }
        }

        // Adicionar loading aos botões de quantidade
        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                // O formulário já será submetido automaticamente
                setTimeout(() => {
                    this.closest('form').submit();
                }, 100);
            });
        });

        // Adicionar confirmação para remoção de itens
        document.querySelectorAll('.btn-remover').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const nomeItem = this.closest('.carrinho-item').querySelector('.item-nome').textContent;
                if (!confirm(`Deseja remover "${nomeItem}" do carrinho?`)) {
                    e.preventDefault();
                }
            });
        });

        // Auto-hide alerts após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>

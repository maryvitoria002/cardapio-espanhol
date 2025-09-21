<?php 
session_start();

// Verifica se o usuário está logado ANTES de incluir o header
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

// Verifica se há itens no carrinho
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    $_SESSION['erro_checkout'] = "¡Tu carrito está vacío!";
    header("Location: ./carrinho.php");
    exit();
}

$titulo = "checkout";
include_once "./components/_base-header.php";

// Verificar mensagens de erro
$erro_checkout = '';
if (isset($_SESSION['erro_checkout'])) {
    $erro_checkout = $_SESSION['erro_checkout'];
    unset($_SESSION['erro_checkout']);
}

// Buscar dados do usuário para pré-preencher endereço
try {
    require_once "db/conection.php";
    $database = new Database();
    $conexao = $database->getInstance();
    
    $stmt = $conexao->prepare("SELECT primeiro_nome, segundo_nome, telefone FROM usuario WHERE id_usuario = ?");
    $stmt->execute([$_SESSION['id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $usuario = [];
}

// Calcular total do carrinho
require_once "./controllers/produto/Crud_produto.php";

$total = 0;
$frete = 2.00; // Taxa de entrega fixa
$itensCarrinho = [];
$erro_debug = '';

try {
    $crudProduto = new Crud_produto();
    
    foreach ($_SESSION['carrinho'] as $item) {
        $produto = $crudProduto->readById($item['id_produto']);
        if ($produto) {
            $subtotal = $produto['preco'] * $item['quantidade'];
            $total += $subtotal;
            
            $itensCarrinho[] = [
                'produto' => $produto,
                'quantidade' => $item['quantidade'],
                'subtotal' => $subtotal
            ];
        }
    }
    
    // Adicionar frete ao total
    $totalComFrete = $total + $frete;
} catch (Exception $e) {
    $erro_debug = "Erro ao carregar produtos: " . $e->getMessage();
}
?>



<div class="checkout-container">
    <div class="checkout-header">
        <h1>🛒 Finalizar Pedido</h1>
        <p>Confirma tu información de entrega y finaliza tu pedido</p>
    </div>
    
    <?php if (!empty($erro_checkout)): ?>
        <div class="alert alert-danger">
            ❌ <?= htmlspecialchars($erro_checkout) ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="./checkout_processar.php" id="checkout-form">
        <div class="checkout-sections">
            <!-- Seção de Endereço -->
            <div class="section">
                <h2>📍 Dirección de Entrega</h2>
                
                <div id="form-endereco">
                    <div class="form-group">
                        <label for="endereco_entrega">Dirección Completa *</label>
                        <textarea name="endereco_entrega" id="endereco_entrega" 
                                  placeholder="Calle, número, complemento, barrio, ciudad - Código Postal"
                                  required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="referencia">Punto de Referencia (opcional)</label>
                        <input type="text" name="referencia" id="referencia" 
                               placeholder="Ej: Cerca del mercado, casa azul...">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="telefone_contato">Teléfono de Contacto *</label>
                    <input type="tel" name="telefone_contato" id="telefone_contato" 
                           value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>"
                           placeholder="(11) 99999-9999" required>
                </div>
                
                <div class="form-group">
                    <label for="modo_pagamento">Forma de Pago *</label>
                    <select name="modo_pagamento" id="modo_pagamento" required>
                        <option value="">Selecciona...</option>
                        <option value="Dinheiro">💵 Efectivo</option>
                        <option value="Cartão de Débito">💳 Tarjeta de Débito</option>
                        <option value="Cartão de Crédito">💳 Tarjeta de Crédito</option>
                        <option value="PIX">📱 PIX</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="observacoes">Observaciones (opcional)</label>
                    <textarea name="observacoes" id="observacoes" 
                              placeholder="Instrucciones especiales para el repartidor..."></textarea>
                </div>
            </div>
            
            <!-- Seção de Resumo -->
            <div class="section">
                <h2>📋 Resumen del Pedido</h2>
                
                <?php foreach ($itensCarrinho as $item): ?>
                    <div class="resumo-linha">
                        <span class="item-nome"><?= htmlspecialchars($item['produto']['nome_produto']) ?></span>
                        <span class="item-quantidade"><?= $item['quantidade'] ?>x</span>
                        <span class="item-preco">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="resumo-linha">
                    <span>Subtotal</span>
                    <span></span>
                    <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
                </div>
                
                <div class="resumo-linha">
                    <span>Tasa de Entrega</span>
                    <span></span>
                    <span>R$ <?= number_format($frete, 2, ',', '.') ?></span>
                </div>
                
                <div class="resumo-linha total">
                    <span>TOTAL</span>
                    <span></span>
                    <span>R$ <?= number_format($totalComFrete, 2, ',', '.') ?></span>
                </div>
                
                <div class="alert alert-success">
                    <small><strong>ℹ️ Información:</strong><br>
                    • Tiempo estimado de entrega: 30-45 minutos<br>
                    • Tasa de entrega: R$ 2,00 (ya incluida en el valor total)<br>
                    • Formas de pago aceptadas en la entrega</small>
                </div>
                
                <!-- BOTÃO FINALIZAR AQUI EMBAIXO DAS INFORMAÇÕES -->
                <div class="checkout-actions">
                    <button type="submit" class="btn btn-success">
                        🛒 FINALIZAR PEDIDO - R$ <?= number_format($totalComFrete, 2, ',', '.') ?>
                    </button>
                    <br><br>
                    <a href="carrinho.php" class="btn btn-secondary">← Volver al Carrito</a>
                </div>
            </div>
            </div>
        </div>
        
        <!-- Campo hidden para endereço quando usar o salvo -->
        <input type="hidden" name="endereco_salvo" value="">
    </form>
</div>

<script>
// Validação do formulário
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const enderecoTextarea = document.getElementById('endereco_entrega');
    
    // Verificar se tem endereço
    if (!enderecoTextarea.value.trim()) {
        alert('Por favor, completa la dirección de entrega.');
        e.preventDefault();
        return;
    }
    
    // Confirmação final
    const total = '<?= number_format($totalComFrete, 2, ',', '.') ?>';
    if (!confirm(`¿Confirmar pedido por un valor de R$ ${total}?`)) {
        e.preventDefault();
    }
});

// Máscara para telefone
document.getElementById('telefone_contato').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    e.target.value = value;
});
</script>

<?php include_once "./components/_base-footer.php"; ?>
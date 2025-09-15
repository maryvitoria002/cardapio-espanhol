<?php 
$titulo = "checkout";
include_once "./components/_base-header.php";

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
    
    $stmt = $conexao->prepare("SELECT primeiro_nome, segundo_nome, endereco, telefone FROM usuario WHERE id_usuario = ?");
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
        <p>Confirme suas informações de entrega e finalize seu pedido</p>
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
                <h2>📍 Endereço de Entrega</h2>
                
                <?php if (!empty($usuario['endereco'])): ?>
                    <div class="endereco-salvo">
                        <strong>Endereço cadastrado:</strong><br>
                        <?= htmlspecialchars($usuario['endereco']) ?>
                        <br><br>
                        <label>
                            <input type="checkbox" id="usar-endereco-salvo" checked onchange="toggleEndereco()">
                            Usar este endereço
                        </label>
                    </div>
                <?php endif; ?>
                
                <div id="form-endereco" class="<?= !empty($usuario['endereco']) ? 'hidden' : '' ?>">
                    <div class="form-group">
                        <label for="endereco_entrega">Endereço Completo *</label>
                        <textarea name="endereco_entrega" id="endereco_entrega" 
                                  placeholder="Rua, número, complemento, bairro, cidade - CEP"
                                  required><?= !empty($usuario['endereco']) ? htmlspecialchars($usuario['endereco']) : '' ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="referencia">Ponto de Referência (opcional)</label>
                        <input type="text" name="referencia" id="referencia" 
                               placeholder="Ex: Próximo ao mercado, casa azul...">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="telefone_contato">Telefone para Contato *</label>
                    <input type="tel" name="telefone_contato" id="telefone_contato" 
                           value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>"
                           placeholder="(11) 99999-9999" required>
                </div>
                
                <div class="form-group">
                    <label for="modo_pagamento">Forma de Pagamento *</label>
                    <select name="modo_pagamento" id="modo_pagamento" required>
                        <option value="">Selecione...</option>
                        <option value="Dinheiro">💵 Dinheiro</option>
                        <option value="Cartão de Débito">💳 Cartão de Débito</option>
                        <option value="Cartão de Crédito">💳 Cartão de Crédito</option>
                        <option value="PIX">📱 PIX</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="observacoes">Observações (opcional)</label>
                    <textarea name="observacoes" id="observacoes" 
                              placeholder="Instruções especiais para o entregador..."></textarea>
                </div>
            </div>
            
            <!-- Seção de Resumo -->
            <div class="section">
                <h2>📋 Resumo do Pedido</h2>
                
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
                    <span>Taxa de Entrega</span>
                    <span></span>
                    <span>R$ <?= number_format($frete, 2, ',', '.') ?></span>
                </div>
                
                <div class="resumo-linha total">
                    <span>TOTAL</span>
                    <span></span>
                    <span>R$ <?= number_format($totalComFrete, 2, ',', '.') ?></span>
                </div>
                
                <div class="alert alert-success">
                    <small><strong>ℹ️ Informações:</strong><br>
                    • Tempo estimado de entrega: 30-45 minutos<br>
                    • Taxa de entrega: R$ 2,00 (já incluída no valor total)<br>
                    • Formas de pagamento aceitas na entrega</small>
                </div>
                
                <!-- BOTÃO FINALIZAR AQUI EMBAIXO DAS INFORMAÇÕES -->
                <div class="checkout-actions">
                    <button type="submit" class="btn btn-success">
                        🛒 FINALIZAR PEDIDO - R$ <?= number_format($totalComFrete, 2, ',', '.') ?>
                    </button>
                    <br><br>
                    <a href="carrinho.php" class="btn btn-secondary">← Voltar ao Carrinho</a>
                </div>
            </div>
            </div>
        </div>
        
        <!-- Campo hidden para endereço quando usar o salvo -->
        <input type="hidden" name="endereco_salvo" value="<?= htmlspecialchars($usuario['endereco'] ?? '') ?>">
    </form>
</div>

<script>
function toggleEndereco() {
    const checkbox = document.getElementById('usar-endereco-salvo');
    const formEndereco = document.getElementById('form-endereco');
    const enderecoTextarea = document.getElementById('endereco_entrega');
    
    if (checkbox.checked) {
        formEndereco.classList.add('hidden');
        enderecoTextarea.required = false;
    } else {
        formEndereco.classList.remove('hidden');
        enderecoTextarea.required = true;
    }
}

// Validação do formulário
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const usarEnderecoSalvo = document.getElementById('usar-endereco-salvo');
    const enderecoTextarea = document.getElementById('endereco_entrega');
    const enderecoSalvo = document.querySelector('input[name="endereco_salvo"]').value;
    
    // Verificar se tem endereço
    if (usarEnderecoSalvo && usarEnderecoSalvo.checked) {
        if (!enderecoSalvo.trim()) {
            alert('Erro: Endereço salvo não encontrado. Por favor, preencha um novo endereço.');
            e.preventDefault();
            return;
        }
    } else {
        if (!enderecoTextarea.value.trim()) {
            alert('Por favor, preencha o endereço de entrega.');
            e.preventDefault();
            return;
        }
    }
    
    // Confirmação final
    const total = '<?= number_format($totalComFrete, 2, ',', '.') ?>';
    if (!confirm(`Confirmar pedido no valor de R$ ${total}?`)) {
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
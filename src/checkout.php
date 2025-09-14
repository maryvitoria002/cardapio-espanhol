<?php 
$titulo = "Finalizar Pedido";
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

<style>
    .checkout-container {
        max-width: 800px;
        margin: 15px auto;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        box-sizing: border-box;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .checkout-header {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }
    
    .checkout-sections {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        box-sizing: border-box;
        max-width: 100%;
        overflow-wrap: break-word;
    }
    
    .section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: #495057;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }
    
    .endereco-salvo {
        background: #e8f4fd;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 15px;
        border-left: 4px solid #007bff;
    }
    
    #form-endereco {
        margin-top: 20px !important;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #495057;
    }
    
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }
    
    .form-group textarea {
        resize: vertical;
        height: 80px;
    }
    
    .resumo-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .resumo-item:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 16px;
        margin-top: 10px;
        padding-top: 15px;
        border-top: 2px solid #007bff;
    }
    
    .item-nome {
        flex: 1;
    }
    
    .item-quantidade {
        margin: 0 15px;
        color: #6c757d;
    }
    
    .item-preco {
        font-weight: bold;
    }
    
    .checkout-actions {
        text-align: center;
        margin-top: 30px;
    }
    
    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
        margin: 0 10px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .btn-primary {
        background: #007bff;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0056b3;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #545b62;
    }
    
    .endereco-salvo {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        border: 1px solid #c3e6cb;
    }
    
    @media (max-width: 768px) {
        .checkout-sections {
            grid-template-columns: 1fr;
        }
        
        .checkout-container {
            margin: 10px;
            padding: 15px;
            max-width: 95%;
        }
        
        .checkout-sections {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .section {
            padding: 15px;
            width: 100%;
            min-width: 0;
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            min-width: 0;
            max-width: 100%;
        }
    }
    
    /* Garantir que nada estoure o container */
    * {
        box-sizing: border-box;
    }
    
    .checkout-container * {
        max-width: 100%;
        word-wrap: break-word;
    }
</style>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>🛒 Finalizar Pedido</h1>
        <p>Confirme suas informações de entrega e finalize seu pedido</p>
    </div>
    
    <?php if (!empty($erro_checkout)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            ❌ <?= htmlspecialchars($erro_checkout) ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="./checkout_processar.php" id="checkout-form">
        <div class="checkout-sections">
            <!-- Seção de Endereço -->
            <div class="section">
                <h3>📍 Endereço de Entrega</h3>
                
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
                
                <div id="form-endereco" <?= !empty($usuario['endereco']) ? 'style="display:none; margin-top: 20px;"' : 'style="margin-top: 20px;"' ?>>
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
                <h3>📋 Resumo do Pedido</h3>
                
                <?php foreach ($itensCarrinho as $item): ?>
                    <div class="resumo-item">
                        <span class="item-nome"><?= htmlspecialchars($item['produto']['nome_produto']) ?></span>
                        <span class="item-quantidade"><?= $item['quantidade'] ?>x</span>
                        <span class="item-preco">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="resumo-item">
                    <span>Subtotal</span>
                    <span></span>
                    <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
                </div>
                
                <div class="resumo-item">
                    <span>Taxa de Entrega</span>
                    <span></span>
                    <span>R$ <?= number_format($frete, 2, ',', '.') ?></span>
                </div>
                
                <div class="resumo-item" style="font-weight: bold; border-top: 2px solid #007bff; padding-top: 10px; margin-top: 10px;">
                    <span>TOTAL</span>
                    <span></span>
                    <span>R$ <?= number_format($totalComFrete, 2, ',', '.') ?></span>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;">
                    <small><strong>ℹ️ Informações:</strong><br>
                    • Tempo estimado de entrega: 30-45 minutos<br>
                    • Taxa de entrega: R$ 2,00 (já incluída no valor total)<br>
                    • Formas de pagamento aceitas na entrega</small>
                </div>
                
                <!-- BOTÃO FINALIZAR AQUI EMBAIXO DAS INFORMAÇÕES -->
                <div style="text-align: center; margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <button type="submit" style="background: #28a745 !important; color: white !important; border: none !important; padding: 15px 40px !important; font-size: 18px !important; font-weight: bold !important; border-radius: 8px !important; cursor: pointer !important; box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;">
                        🛒 FINALIZAR PEDIDO - R$ <?= number_format($totalComFrete, 2, ',', '.') ?>
                    </button>
                    <br><br>
                    <a href="carrinho.php" style="color: #6c757d; text-decoration: none; font-size: 14px;">← Voltar ao Carrinho</a>
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
        formEndereco.style.display = 'none';
        enderecoTextarea.required = false;
    } else {
        formEndereco.style.display = 'block';
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
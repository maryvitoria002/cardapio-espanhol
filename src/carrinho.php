<?php 
$titulo = "carrinho";
include_once "./components/_base-header.php";
require_once "./controllers/carrinho/Crud_carrinho.php";
require_once "./controllers/usuario/Crud_usuario.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION["id"])) {
    header("Location: ./login.php");
    exit();
}

$usuario = new Crud_usuario();
$usuario->setId_usuario($_SESSION["id"]);
$res = $usuario->read();
?>

<div class="carrinho-container">
    <div class="header-carrinho">
        <h1>Meu Carrinho</h1>
        <p class="welcome-text">Olá, <?= htmlspecialchars(
            trim(($res['primeiro_nome'] ?? '') . ' ' . ($res['segundo_nome'] ?? ''))
            ?: 'Visitante'
        ) ?>!</p>
    </div>

    <div class="carrinho-content">
        <div class="items-section">
            <div class="card">
                <div class="card-header">
                    <h5>Seus Itens</h5>
                </div>
                <div class="card-body">
                    <?php
                    $carrinho = new Crud_carrinho();
                    $carrinho->setId_usuario($_SESSION['id']);
                    $itens = $carrinho->read();

                    if ($itens && count($itens) > 0):
                        foreach ($itens as $item):
                    ?>
                        <div class="cart-item">
                            <div class="item-details">
                                <div class="item-image">
                                    <img src="./assets/produto-default.png" alt="Produto">
                                </div>
                                <div class="item-info">
                                    <h6><?= htmlspecialchars($item['id_produto']) ?></h6>
                                    <p class="price">R$ <?= number_format($item['quantidade'], 2, ',', '.') ?></p>
                                </div>
                            </div>
                            <div class="item-actions">
                                <div class="quantity-control">
                                    <button class="btn-quantity" onclick="updateQuantidade(<?= $item['id_carrinho'] ?>, 'diminuir')">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="quantity"><?= $item['quantidade'] ?></span>
                                    <button class="btn-quantity" onclick="updateQuantidade(<?= $item['id_carrinho'] ?>, 'aumentar')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <button class="btn-remove" onclick="removeItem(<?= $item['id_carrinho'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php 
                        endforeach;
                    else:
                    ?>
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Seu carrinho está vazio</p>
                            <a href="./cardapio.php" class="btn-shop">Ver Cardápio</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="summary-section">
            <div class="card">
                <div class="card-header">
                    <h5>Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>R$ <?= number_format(0, 2, ',', '.') ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Taxa de entrega</span>
                        <span>R$ <?= number_format(0, 2, ',', '.') ?></span>
                    </div>
                    <hr>
                    <div class="summary-total">
                        <span>Total</span>
                        <span>R$ <?= number_format(0, 2, ',', '.') ?></span>
                    </div>
                    <button class="btn-checkout">
                        <i class="fas fa-lock"></i>
                        Finalizar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateQuantidade(id, acao) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('acao', acao);

    fetch('controllers/carrinho/atualizar_quantidade.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao atualizar quantidade');
        }
    });
}

function removeItem(id) {
    if (confirm('Tem certeza que deseja remover este item?')) {
        fetch('controllers/carrinho/remover_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao remover item');
            }
        });
    }
}
</script>

<style>
.carrinho-container {
    padding: 2rem;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
    width: 100%;
}

.header-carrinho {
    margin-bottom: 2rem;
    text-align: center;
}

.header-carrinho h1 {
    color: #333;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.welcome-text {
    color: #666;
    font-size: 1.1rem;
}

.carrinho-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: #fff;
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
}

.card-header h5 {
    margin: 0;
    color: #333;
    font-size: 1.25rem;
}

.card-body {
    padding: 1.5rem;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #eee;
    transition: all 0.3s ease;
}

.cart-item:hover {
    background: #f8f9fa;
}

.item-details {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.item-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-info h6 {
    margin: 0;
    color: #333;
    font-size: 1.1rem;
}

.price {
    color: #666;
    margin: 0.5rem 0 0 0;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 8px;
}

.btn-quantity {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
}

.quantity {
    min-width: 40px;
    text-align: center;
    font-weight: bold;
}

.btn-remove {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    margin-left: 1rem;
}

.empty-cart {
    text-align: center;
    padding: 3rem 0;
}

.empty-cart i {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 1rem;
}

.empty-cart p {
    color: #666;
    margin-bottom: 1.5rem;
}

.btn-shop {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-shop:hover {
    background: #0056b3;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    color: #666;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.25rem;
    font-weight: bold;
    color: #333;
    margin-top: 1rem;
}

.btn-checkout {
    width: 100%;
    padding: 1rem;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    margin-top: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-checkout:hover {
    background: #218838;
}

@media (max-width: 768px) {
    .carrinho-content {
        grid-template-columns: 1fr;
    }
}
</style>

<?php 
include_once "./components/_base-footer.php";
?>


<?php 
$titulo = "historico";
include_once "./components/_base-header.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

require_once "./controllers/pedido/Crud_pedido.php";
$pedidos = new Crud_pedido();
$pedidos->setId_usuario($_SESSION['id']);
$historico = $pedidos->readAll(); // Método que retorna todos os pedidos do usuário
?>

<div class="historico-container">
    <h1>Histórico de Pedidos</h1>

    <?php if ($historico && count($historico) > 0): ?>
        <div class="pedidos-lista">
            <?php foreach ($historico as $pedido): ?>
                <div class="pedido-card">
                    <div class="pedido-header">
                        <div class="pedido-info">
                            <h3>Pedido #<?= htmlspecialchars($pedido['id_pedido']) ?></h3>
                            <span class="data">
                                <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?>
                            </span>
                        </div>
                        <div class="pedido-status <?= strtolower($pedido['status']) ?>">
                            <?= htmlspecialchars($pedido['status']) ?>
                        </div>
                    </div>

                    <div class="pedido-itens">
                        <?php foreach ($pedido['itens'] as $item): ?>
                            <div class="item">
                                <span class="quantidade"><?= $item['quantidade'] ?>x</span>
                                <span class="nome"><?= htmlspecialchars($item['nome_produto']) ?></span>
                                <span class="preco">
                                    R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="pedido-footer">
                        <div class="total">
                            <span>Total do Pedido:</span>
                            <strong>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></strong>
                        </div>
                        <button class="btn-detalhes" onclick="verDetalhes(<?= $pedido['id_pedido'] ?>)">
                            Ver Detalhes
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="sem-pedidos">
            <img src="./assets/empty-order.png" alt="Sem pedidos">
            <h2>Você ainda não fez nenhum pedido</h2>
            <p>Que tal experimentar nossa deliciosa comida?</p>
            <a href="./cardapio.php" class="btn-cardapio">Ver Cardápio</a>
        </div>
    <?php endif; ?>
</div>

<style>
.historico-container {
    padding: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.historico-container h1 {
    color: #333;
    margin-bottom: 2rem;
    text-align: center;
}

.pedidos-lista {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.pedido-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.pedido-header {
    padding: 1.5rem;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pedido-info h3 {
    margin: 0;
    color: #333;
    font-size: 1.2rem;
}

.data {
    color: #666;
    font-size: 0.9rem;
}

.pedido-status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.pedido-status.pendente { background: #fff3cd; color: #856404; }
.pedido-status.preparando { background: #cce5ff; color: #004085; }
.pedido-status.entregue { background: #d4edda; color: #155724; }
.pedido-status.cancelado { background: #f8d7da; color: #721c24; }

.pedido-itens {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
}

.item {
    display: flex;
    align-items: center;
    margin-bottom: 0.8rem;
}

.quantidade {
    font-weight: bold;
    margin-right: 1rem;
    color: #666;
}

.nome {
    flex: 1;
}

.preco {
    color: #333;
    font-weight: 500;
}

.pedido-footer {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total {
    color: #333;
}

.total strong {
    font-size: 1.2rem;
    margin-left: 0.5rem;
}

.btn-detalhes {
    padding: 0.8rem 1.5rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-detalhes:hover {
    background: #0056b3;
}

.sem-pedidos {
    text-align: center;
    padding: 3rem 0;
}

.sem-pedidos img {
    width: 200px;
    margin-bottom: 2rem;
}

.sem-pedidos h2 {
    color: #333;
    margin-bottom: 1rem;
}

.sem-pedidos p {
    color: #666;
    margin-bottom: 2rem;
}

.btn-cardapio {
    display: inline-block;
    padding: 1rem 2rem;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background 0.3s ease;
}

.btn-cardapio:hover {
    background: #218838;
}

@media (max-width: 768px) {
    .historico-container {
        padding: 1rem;
    }

    .pedido-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .pedido-footer {
        flex-direction: column;
        gap: 1rem;
    }

    .total {
        text-align: center;
        width: 100%;
    }

    .btn-detalhes {
        width: 100%;
    }
}
</style>

<script>
function verDetalhes(idPedido) {
    // Implemente a lógica para mostrar mais detalhes do pedido
    // Pode ser um modal ou redirecionamento para uma página específica
    window.location.href = `./detalhes-pedido.php?id=${idPedido}`;
}
</script>

<?php 
include_once "./components/_base-footer.php";
?>


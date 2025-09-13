<?php 
$titulo = "historico";
include_once "./components/_base-header.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

// Exibir mensagens de sucesso
$mensagem_sucesso = '';
if (isset($_SESSION['sucesso_checkout'])) {
    $mensagem_sucesso = $_SESSION['sucesso_checkout'];
    unset($_SESSION['sucesso_checkout']);
}

require_once "./controllers/pedido/Crud_pedido.php";
$crudPedidos = new Crud_pedido();

try {
    $historico = $crudPedidos->readByUser($_SESSION['id']);
} catch (Exception $e) {
    $historico = [];
    $erro = "Erro ao carregar histórico de pedidos: " . $e->getMessage();
}
?>

<div class="historico-container">
    <h1>Histórico de Pedidos</h1>
    
    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($mensagem_sucesso) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

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
                        <div class="pedido-status <?= strtolower($pedido['status_pedido']) ?>">
                            <?= htmlspecialchars($pedido['status_pedido']) ?>
                        </div>
                    </div>

                    <div class="pedido-itens">
                        <?php if (isset($pedido['itens']) && count($pedido['itens']) > 0): ?>
                            <?php foreach ($pedido['itens'] as $item): ?>
                                <div class="item">
                                    <span class="quantidade"><?= $item['quantidade'] ?>x</span>
                                    <span class="nome"><?= htmlspecialchars($item['nome_produto']) ?></span>
                                    <span class="preco">
                                        R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="item">
                                <span class="nome">Itens não encontrados</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pedido-footer">
                        <div class="total">
                            <span>Total do Pedido:</span>
                            <strong>R$ <?= number_format($pedido['total_pedido'], 2, ',', '.') ?></strong>
                        </div>
                        <div class="info-adicional">
                            <span><?= $pedido['total_itens'] ?> item(s)</span>
                        </div>
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
/* Reset específico para evitar conflitos */
.historico-container * {
    box-sizing: border-box;
}

/* Container principal */
.historico-container {
    width: 100% !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 2rem !important;
    background-color: #f8f9fa !important;
    min-height: 100vh !important;
    max-height: 100vh !important;
    display: block !important;
    clear: both !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

.historico-container h1 {
    color: #333 !important;
    margin-bottom: 2rem !important;
    text-align: center !important;
    font-size: 2.5rem !important;
    font-weight: 700 !important;
    width: 100% !important;
    display: block !important;
}

/* Lista de pedidos - forçar disposição vertical */
.pedidos-lista {
    display: block !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    max-height: calc(100vh - 200px) !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

/* Card do pedido - garantir que seja vertical */
.pedido-card {
    background: white !important;
    border-radius: 15px !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    overflow: hidden !important;
    width: 100% !important;
    margin: 0 0 2rem 0 !important;
    padding: 0 !important;
    border: 1px solid #e9ecef !important;
    display: block !important;
    float: none !important;
    clear: both !important;
    max-width: 100% !important;
    word-wrap: break-word !important;
}

.pedido-header {
    padding: 1.5rem !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 1rem !important;
    width: 100% !important;
}

.pedido-info {
    flex: 1 !important;
}

.pedido-info h3 {
    margin: 0 !important;
    color: white !important;
    font-size: 1.4rem !important;
    font-weight: 600 !important;
}

.pedido-info .data {
    color: rgba(255,255,255,0.9) !important;
    font-size: 0.95rem !important;
    margin-top: 0.5rem !important;
    display: block !important;
}

.pedido-status {
    padding: 0.6rem 1.2rem !important;
    border-radius: 25px !important;
    font-size: 0.9rem !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.pedido-status.pendente { 
    background: #fff3cd !important; 
    color: #856404 !important; 
    border: 1px solid #ffeaa7 !important;
}

.pedido-status.preparando { 
    background: #cce5ff !important; 
    color: #004085 !important; 
    border: 1px solid #74b9ff !important;
}

.pedido-status.entregue { 
    background: #d4edda !important; 
    color: #155724 !important; 
    border: 1px solid #00b894 !important;
}

.pedido-status.cancelado { 
    background: #f8d7da !important; 
    color: #721c24 !important; 
    border: 1px solid #e84393 !important;
}

.pedido-itens {
    padding: 1.5rem !important;
    border-bottom: 1px solid #eee !important;
    background: #fff !important;
    width: 100% !important;
    display: block !important;
}

.pedido-itens .item {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 0.8rem 0 !important;
    border-bottom: 1px solid #f8f9fa !important;
    width: 100% !important;
    margin: 0 !important;
}

.pedido-itens .item:last-child {
    border-bottom: none !important;
}

.pedido-itens .quantidade {
    font-weight: bold !important;
    color: #667eea !important;
    min-width: 60px !important;
    font-size: 1rem !important;
    flex-shrink: 0 !important;
}

.pedido-itens .nome {
    flex: 1 !important;
    margin: 0 1rem !important;
    color: #333 !important;
    font-size: 1rem !important;
}

.pedido-itens .preco {
    color: #28a745 !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    flex-shrink: 0 !important;
}

.pedido-footer {
    padding: 1.5rem !important;
    background: #f8f9fa !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 1rem !important;
    width: 100% !important;
}

.pedido-footer .total {
    color: #333 !important;
    font-size: 1.1rem !important;
}

.pedido-footer .total strong {
    color: #28a745 !important;
    font-size: 1.4rem !important;
    margin-left: 0.5rem !important;
    font-weight: 700 !important;
}

.pedido-footer .info-adicional {
    color: #666 !important;
    font-size: 0.95rem !important;
    background: #e9ecef !important;
    padding: 0.5rem 1rem !important;
    border-radius: 20px !important;
}

.sem-pedidos {
    text-align: center !important;
    padding: 4rem 2rem !important;
    background: white !important;
    border-radius: 15px !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    width: 100% !important;
    display: block !important;
}

.sem-pedidos h2 {
    color: #333 !important;
    margin-bottom: 1rem !important;
    font-size: 1.8rem !important;
}

.sem-pedidos p {
    color: #666 !important;
    margin-bottom: 2rem !important;
    font-size: 1.1rem !important;
}

.btn-cardapio {
    display: inline-block !important;
    padding: 1rem 2rem !important;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: white !important;
    text-decoration: none !important;
    border-radius: 25px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.btn-cardapio:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3) !important;
}

/* Alertas */
.alert {
    padding: 1rem 1.5rem !important;
    margin: 0 0 2rem 0 !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.8rem !important;
    font-size: 1rem !important;
    font-weight: 500 !important;
    width: 100% !important;
}

.alert-success {
    background-color: #d4edda !important;
    color: #155724 !important;
    border: 1px solid #c3e6cb !important;
}

.alert-danger {
    background-color: #f8d7da !important;
    color: #721c24 !important;
    border: 1px solid #f5c6cb !important;
}

.alert i {
    font-size: 1.2rem !important;
}

/* Responsividade */
@media (max-width: 768px) {
    .historico-container {
        padding: 1rem !important;
    }

    .historico-container h1 {
        font-size: 2rem !important;
    }

    .pedido-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
        text-align: left !important;
    }

    .pedido-footer {
        flex-direction: column !important;
        gap: 1rem !important;
        text-align: center !important;
    }

    .pedido-itens .item {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0.5rem !important;
    }

    .pedido-itens .nome {
        margin: 0 !important;
    }

    .sem-pedidos {
        padding: 2rem 1rem !important;
    }
}

@media (max-width: 480px) {
    .historico-container {
        padding: 0.5rem !important;
    }

    .pedido-card {
        margin-bottom: 1.5rem !important;
    }

    .pedido-header,
    .pedido-itens,
    .pedido-footer {
        padding: 1rem !important;
    }
}

/* Garantir que nada flutue ou saia do container */
.historico-container::after {
    content: "";
    display: table;
    clear: both;
}

/* Customizar scrollbar */
.historico-container::-webkit-scrollbar,
.pedidos-lista::-webkit-scrollbar {
    width: 8px;
}

.historico-container::-webkit-scrollbar-track,
.pedidos-lista::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.historico-container::-webkit-scrollbar-thumb,
.pedidos-lista::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.historico-container::-webkit-scrollbar-thumb:hover,
.pedidos-lista::-webkit-scrollbar-thumb:hover {
    background: #555;
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


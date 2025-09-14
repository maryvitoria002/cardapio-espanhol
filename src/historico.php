<?php 
$titulo = "historico";
include_once "./components/_base-header.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

// Processar ações de cancelamento e avaliação
$mensagem_acao = '';

if (isset($_GET['acao']) && isset($_GET['id'])) {
    $acao = $_GET['acao'];
    $id_pedido = intval($_GET['id']);
    
    if ($acao === 'cancelar' && $id_pedido > 0) {
        // Verificar se é confirmação ou primeira tentativa
        $confirmar = isset($_GET['confirmar']) && $_GET['confirmar'] === 'sim';
        
        if (!$confirmar) {
            // Primeira tentativa - mostrar confirmação
            $mensagem_acao = "⚠️ Tem certeza que deseja cancelar o pedido #$id_pedido? 
                              <a href='?acao=cancelar&id=$id_pedido&confirmar=sim' 
                                 style='background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin: 0 5px;'>
                                 ✅ SIM, CANCELAR
                              </a>
                              <a href='?' 
                                 style='background: #6c757d; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin: 0 5px;'>
                                 ❌ NÃO
                              </a>";
        } else {
            // Confirmação recebida - prosseguir com cancelamento
            try {
                require_once "db/conection.php";
                $database = new Database();
                $conexao = $database->getInstance();
                
                // Debug das sessões
                $user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
                
                if (!$user_id) {
                    $mensagem_acao = "❌ Erro: Usuário não identificado na sessão";
                } else {
                    // Verificar se o pedido pertence ao usuário e está pendente
                    $stmt = $conexao->prepare("SELECT status_pedido, id_usuario FROM pedido WHERE id_pedido = ?");
                    $stmt->execute([$id_pedido]);
                    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$pedido) {
                        $mensagem_acao = "❌ Pedido #$id_pedido não encontrado";
                    } elseif ($pedido['id_usuario'] != $user_id) {
                        $mensagem_acao = "❌ Este pedido não pertence a você";
                    } elseif (strtolower($pedido['status_pedido']) !== 'pendente') {
                        $mensagem_acao = "❌ Só é possível cancelar pedidos pendentes. Status atual: " . $pedido['status_pedido'];
                    } else {
                        // Cancelar o pedido
                        $stmt = $conexao->prepare("UPDATE pedido SET status_pedido = 'Cancelado' WHERE id_pedido = ?");
                        $resultado = $stmt->execute([$id_pedido]);
                        
                        if ($resultado) {
                            $mensagem_acao = "✅ Pedido #$id_pedido cancelado com sucesso!";
                        } else {
                            $mensagem_acao = "❌ Falha ao executar o cancelamento";
                        }
                    }
                }
            } catch (Exception $e) {
                $mensagem_acao = "❌ Erro ao cancelar pedido: " . $e->getMessage();
            }
        }
    } elseif ($acao === 'avaliar' && $id_pedido > 0) {
        // Verificar se já foi avaliado
        try {
            require_once "db/conection.php";
            $database = new Database();
            $conexao = $database->getInstance();
            
            $user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
            
            // Verificar se o pedido existe e pertence ao usuário
            $stmt = $conexao->prepare("SELECT status_pedido, id_usuario FROM pedido WHERE id_pedido = ?");
            $stmt->execute([$id_pedido]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pedido) {
                $mensagem_acao = "❌ Pedido não encontrado";
            } elseif ($pedido['id_usuario'] != $user_id) {
                $mensagem_acao = "❌ Este pedido não pertence a você";
            } elseif (strtolower($pedido['status_pedido']) !== 'concluido') {
                $mensagem_acao = "❌ Só é possível avaliar pedidos concluídos";
            } else {
                // Verificar se já foi avaliado
                $stmt = $conexao->prepare("SELECT id_avaliacao FROM avaliacao WHERE id_pedido = ?");
                $stmt->execute([$id_pedido]);
                $jaAvaliado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($jaAvaliado) {
                    $mensagem_acao = "❌ Este pedido já foi avaliado";
                } else {
                    // Redirecionar para formulário de avaliação
                    header("Location: ./avaliar_pedido.php?id=" . $id_pedido);
                    exit();
                }
            }
        } catch (Exception $e) {
            $mensagem_acao = "❌ Erro ao processar avaliação: " . $e->getMessage();
        }
    }
    
    // Redirecionar para limpar os parâmetros da URL
    if ($mensagem_acao) {
        $_SESSION['mensagem_historico'] = $mensagem_acao;
        header("Location: ./historico.php");
        exit();
    }
}

// Exibir mensagem se existir
if (isset($_SESSION['mensagem_historico'])) {
    $mensagem_acao = $_SESSION['mensagem_historico'];
    unset($_SESSION['mensagem_historico']);
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
    
    <!-- DEBUG INFO -->
    <div style="background: #e9ecef; padding: 10px; margin: 10px 0; border-radius: 4px; font-family: monospace; font-size: 12px;">
        <strong>🔍 DEBUG:</strong><br>
        - Ação GET: <?= $_GET['acao'] ?? 'nenhuma' ?><br>
        - ID GET: <?= $_GET['id'] ?? 'nenhum' ?><br>
        - Session ID: <?= $_SESSION['id'] ?? 'não definido' ?><br>
        - Mensagem ação: <?= $mensagem_acao ?? 'nenhuma' ?><br>
        - Timestamp: <?= date('H:i:s') ?>
    </div>
    
    <?php if ($mensagem_sucesso): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($mensagem_sucesso) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($mensagem_acao): ?>
        <div class="alert alert-info" style="background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 15px; border-radius: 4px; margin: 10px 0;">
            <?= $mensagem_acao ?>
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
                        
                        <!-- Botões de ação baseados no status -->
                        <div class="pedido-acoes">
                            <?php if (strtolower($pedido['status_pedido']) === 'pendente'): ?>
                                <a href="?acao=cancelar&id=<?= $pedido['id_pedido'] ?>" 
                                   style="background: #dc3545; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; margin: 5px; display: inline-block;">
                                    ❌ Cancelar Pedido
                                </a>
                            <?php elseif (strtolower($pedido['status_pedido']) === 'concluido'): ?>
                                <!-- Verificar se já foi avaliado -->
                                <?php
                                try {
                                    $database = new Database();
                                    $conexao = $database->getInstance();
                                    $stmt = $conexao->prepare("SELECT id_avaliacao FROM avaliacao WHERE id_pedido = ?");
                                    $stmt->execute([$pedido['id_pedido']]);
                                    $jaAvaliado = $stmt->fetch(PDO::FETCH_ASSOC);
                                } catch (Exception $e) {
                                    $jaAvaliado = false;
                                }
                                ?>
                                
                                <?php if (!$jaAvaliado): ?>
                                    <a href="?acao=avaliar&id=<?= $pedido['id_pedido'] ?>" 
                                       style="background: #28a745; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; margin: 5px; display: inline-block;">
                                        ⭐ Avaliar Pedido
                                    </a>
                                <?php else: ?>
                                    <span class="ja-avaliado">
                                        Já avaliado
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
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

.pedido-status.processando { 
    background: #cce5ff !important; 
    color: #004085 !important; 
    border: 1px solid #74b9ff !important;
}

.pedido-status.caminho { 
    background: #e1f5fe !important; 
    color: #0277bd !important; 
    border: 1px solid #29b6f6 !important;
}

.pedido-status.concluido { 
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

/* Botões de ação */
.pedido-acoes {
    margin-top: 15px !important;
    display: flex !important;
    gap: 10px !important;
    align-items: center !important;
}

.btn-cancelar, .btn-avaliar {
    padding: 8px 16px !important;
    border: none !important;
    border-radius: 6px !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
    display: inline-block !important;
}

.btn-cancelar {
    background-color: #dc3545 !important;
    color: white !important;
}

.btn-cancelar:hover {
    background-color: #c82333 !important;
    transform: translateY(-1px) !important;
    color: white !important;
    text-decoration: none !important;
}

.btn-avaliar {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.btn-avaliar:hover {
    background-color: #e0a800 !important;
    transform: translateY(-1px) !important;
}

.ja-avaliado {
    color: #28a745 !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}

/* Modal styles */
.modal {
    display: none !important;
    position: fixed !important;
    z-index: 1000 !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background-color: rgba(0,0,0,0.5) !important;
}

.modal-content {
    background-color: white !important;
    margin: 10% auto !important;
    padding: 20px !important;
    border-radius: 10px !important;
    width: 90% !important;
    max-width: 500px !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;
}

.modal-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    margin-bottom: 20px !important;
    padding-bottom: 10px !important;
    border-bottom: 1px solid #eee !important;
}

.modal-header h3 {
    margin: 0 !important;
    color: #333 !important;
}

.close {
    background: none !important;
    border: none !important;
    font-size: 24px !important;
    cursor: pointer !important;
    color: #999 !important;
}

.close:hover {
    color: #333 !important;
}

.form-group {
    margin-bottom: 15px !important;
}

.form-group label {
    display: block !important;
    margin-bottom: 5px !important;
    font-weight: 500 !important;
    color: #333 !important;
}

.form-group input, .form-group textarea, .form-group select {
    width: 100% !important;
    padding: 10px !important;
    border: 1px solid #ddd !important;
    border-radius: 5px !important;
    font-size: 14px !important;
}

.form-group textarea {
    height: 80px !important;
    resize: vertical !important;
}

.rating-input {
    display: flex !important;
    gap: 5px !important;
    margin-bottom: 10px !important;
}

.star {
    font-size: 24px !important;
    color: #ddd !important;
    cursor: pointer !important;
    transition: color 0.2s !important;
}

.star.active, .star:hover {
    color: #ffc107 !important;
}

.modal-actions {
    display: flex !important;
    gap: 10px !important;
    justify-content: flex-end !important;
    margin-top: 20px !important;
}

.btn-modal {
    padding: 10px 20px !important;
    border: none !important;
    border-radius: 5px !important;
    cursor: pointer !important;
    font-size: 14px !important;
    font-weight: 500 !important;
}

.btn-primary {
    background-color: #007bff !important;
    color: white !important;
}

.btn-primary:hover {
    background-color: #0056b3 !important;
}

.btn-secondary {
    background-color: #6c757d !important;
    color: white !important;
}

.btn-secondary:hover {
    background-color: #545b62 !important;
}
</style>

<!-- Modal para cancelar pedido -->
<div id="modalCancelar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Cancelar Pedido</h3>
            <button class="close" onclick="fecharModal('modalCancelar')">&times;</button>
        </div>
        <form id="formCancelar">
            <div class="form-group">
                <label for="motivoCancelamento">Motivo do cancelamento:</label>
                <select id="motivoCancelamento" name="motivo" required>
                    <option value="">Selecione um motivo</option>
                    <option value="Não preciso mais">Não preciso mais</option>
                    <option value="Demora na entrega">Demora na entrega</option>
                    <option value="Preço alto">Preço alto</option>
                    <option value="Mudança de planos">Mudança de planos</option>
                    <option value="Outro">Outro motivo</option>
                </select>
            </div>
            <div class="form-group">
                <label for="observacoesCancelamento">Observações (opcional):</label>
                <textarea id="observacoesCancelamento" name="observacoes" placeholder="Descreva melhor o motivo do cancelamento..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal btn-secondary" onclick="fecharModal('modalCancelar')">Voltar</button>
                <button type="submit" class="btn-modal btn-primary">Confirmar Cancelamento</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para avaliar pedido -->
<div id="modalAvaliar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Avaliar Pedido</h3>
            <button class="close" onclick="fecharModal('modalAvaliar')">&times;</button>
        </div>
        <form id="formAvaliar">
            <div class="form-group">
                <label>Sua nota para este pedido:</label>
                <div class="rating-input">
                    <span class="star" data-rating="1">★</span>
                    <span class="star" data-rating="2">★</span>
                    <span class="star" data-rating="3">★</span>
                    <span class="star" data-rating="4">★</span>
                    <span class="star" data-rating="5">★</span>
                </div>
                <input type="hidden" id="notaAvaliacao" name="nota" required>
            </div>
            <div class="form-group">
                <label for="comentarioAvaliacao">Seu comentário:</label>
                <textarea id="comentarioAvaliacao" name="comentario" placeholder="Conte-nos sobre sua experiência com este pedido..." required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal btn-secondary" onclick="fecharModal('modalAvaliar')">Cancelar</button>
                <button type="submit" class="btn-modal btn-primary">Enviar Avaliação</button>
            </div>
        </form>
    </div>
</div>

<script>
console.log('=== INÍCIO DO SCRIPT HISTORICO.PHP ===');
console.log('Script do histórico carregado!');
let pedidoAtual = null;

// Função para ver detalhes (já existente)
function verDetalhes(idPedido) {
    // Implemente a lógica para mostrar mais detalhes do pedido
    // Pode ser um modal ou redirecionamento para uma página específica
    window.location.href = `./detalhes-pedido.php?id=${idPedido}`;
}

// Função para cancelar pedido
function cancelarPedido(idPedido) {
    console.log('Função cancelarPedido chamada com ID:', idPedido);
    pedidoAtual = idPedido;
    const modal = document.getElementById('modalCancelar');
    console.log('Modal encontrado:', modal);
    if (modal) {
        modal.style.display = 'block';
        console.log('Modal deveria estar visível agora');
    } else {
        console.error('Modal modalCancelar não encontrado!');
    }
}

// Função para avaliar pedido
function avaliarPedido(idPedido) {
    console.log('Função avaliarPedido chamada com ID:', idPedido);
    pedidoAtual = idPedido;
    const modal = document.getElementById('modalAvaliar');
    console.log('Modal encontrado:', modal);
    if (modal) {
        modal.style.display = 'block';
        console.log('Modal deveria estar visível agora');
    } else {
        console.error('Modal modalAvaliar não encontrado!');
    }
    
    // Reset rating
    document.querySelectorAll('.star').forEach(star => {
        star.classList.remove('active');
    });
    document.getElementById('notaAvaliacao').value = '';
}

// Adicionar event listeners após o DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado, verificando elementos...');
    
    // Verificar se os modais existem
    const modalCancelar = document.getElementById('modalCancelar');
    const modalAvaliar = document.getElementById('modalAvaliar');
    
    console.log('Modal cancelar encontrado:', !!modalCancelar);
    console.log('Modal avaliar encontrado:', !!modalAvaliar);
    
    // Verificar se há botões de cancelar e avaliar
    const btnsCancelar = document.querySelectorAll('.btn-cancelar');
    const btnsAvaliar = document.querySelectorAll('.btn-avaliar');
    
    console.log('Botões cancelar encontrados:', btnsCancelar.length);
    console.log('Botões avaliar encontrados:', btnsAvaliar.length);
    
    // Testar automaticamente as funções
    setTimeout(function() {
        console.log('=== TESTE AUTOMÁTICO ===');
        console.log('Testando função cancelarPedido...');
        if (typeof cancelarPedido === 'function') {
            console.log('Função cancelarPedido existe!');
        } else {
            console.error('Função cancelarPedido NÃO EXISTE!');
        }
        
        console.log('Testando função avaliarPedido...');
        if (typeof avaliarPedido === 'function') {
            console.log('Função avaliarPedido existe!');
        } else {
            console.error('Função avaliarPedido NÃO EXISTE!');
        }
    }, 1000);
    
    // Adicionar event listeners aos botões como backup
    btnsCancelar.forEach((btn, index) => {
        console.log('Configurando event listener para botão cancelar', index);
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Event listener disparado para cancelar');
            const onclickAttr = this.getAttribute('onclick');
            console.log('Onclick original:', onclickAttr);
            if (onclickAttr) {
                const idMatch = onclickAttr.match(/\d+/);
                if (idMatch) {
                    const idPedido = parseInt(idMatch[0]);
                    console.log('Event listener - cancelar pedido:', idPedido);
                    cancelarPedido(idPedido);
                }
            }
        });
    });
    
    btnsAvaliar.forEach((btn, index) => {
        console.log('Configurando event listener para botão avaliar', index);
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Event listener disparado para avaliar');
            const onclickAttr = this.getAttribute('onclick');
            console.log('Onclick original:', onclickAttr);
            if (onclickAttr) {
                const idMatch = onclickAttr.match(/\d+/);
                if (idMatch) {
                    const idPedido = parseInt(idMatch[0]);
                    console.log('Event listener - avaliar pedido:', idPedido);
                    avaliarPedido(idPedido);
                }
            }
        });
    });
});

// Função para fechar modal
function fecharModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    pedidoAtual = null;
}

// Sistema de rating com estrelas
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            document.getElementById('notaAvaliacao').value = rating;
            
            // Atualizar visual das estrelas
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
        
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.dataset.rating);
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    // Reset visual on mouse leave
    document.querySelector('.rating-input').addEventListener('mouseleave', function() {
        const currentRating = parseInt(document.getElementById('notaAvaliacao').value) || 0;
        stars.forEach((s, index) => {
            if (index < currentRating) {
                s.style.color = '#ffc107';
            } else {
                s.style.color = '#ddd';
            }
        });
    });
});

// Form de cancelamento
document.getElementById('formCancelar').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!pedidoAtual) return;
    
    const formData = new FormData();
    formData.append('id_pedido', pedidoAtual);
    formData.append('motivo', document.getElementById('motivoCancelamento').value);
    
    const observacoes = document.getElementById('observacoesCancelamento').value;
    if (observacoes) {
        formData.append('motivo', formData.get('motivo') + ': ' + observacoes);
    }
    
    fetch('./cancelar_pedido.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pedido cancelado com sucesso!');
            location.reload();
        } else {
            alert('Erro ao cancelar pedido: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao processar solicitação');
    });
    
    fecharModal('modalCancelar');
});

// Form de avaliação
document.getElementById('formAvaliar').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!pedidoAtual) return;
    
    const nota = document.getElementById('notaAvaliacao').value;
    const comentario = document.getElementById('comentarioAvaliacao').value.trim();
    
    if (!nota) {
        alert('Por favor, selecione uma nota de 1 a 5 estrelas');
        return;
    }
    
    if (comentario.length < 10) {
        alert('Por favor, escreva um comentário com pelo menos 10 caracteres');
        return;
    }
    
    const formData = new FormData();
    formData.append('id_pedido', pedidoAtual);
    formData.append('nota', nota);
    formData.append('comentario', comentario);
    
    fetch('./avaliar_pedido.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Avaliação enviada com sucesso!');
            location.reload();
        } else {
            alert('Erro ao enviar avaliação: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao processar solicitação');
    });
    
    fecharModal('modalAvaliar');
});

// Fechar modal clicando fora dele
window.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
            pedidoAtual = null;
        }
    });
});
</script>

<?php 
include_once "./components/_base-footer.php";
?>


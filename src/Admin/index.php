<?php
session_start();

// Permitir acesso direto - criar sessão admin automática se não existir
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Criar sessão admin automática
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

// Incluir controllers necessários
require_once __DIR__ . '/../db/conection.php';
require_once __DIR__ . '/../models/Crud_usuario.php';
require_once __DIR__ . '/../models/Crud_produto.php';
require_once __DIR__ . '/../models/Crud_pedido.php';
require_once __DIR__ . '/../models/Crud_funcionario.php';
require_once __DIR__ . '/../models/Crud_categoria.php';

// Obter estatísticas
try {
    // Criar conexão
    $database = new Database();
    $conexao = $database->getInstance();
    
    $crudUsuario = new Crud_usuario();
    $crudProduto = new Crud_produto();
    $crudPedido = new Crud_pedido($conexao); // Passar conexão
    $crudFuncionario = new Crud_funcionario();
    $crudCategoria = new Crud_categoria();
    
    // Estatísticas básicas
    $total_usuarios = $crudUsuario->count();
    $usuarios_ativos = $total_usuarios; // Tabela usuario não tem campo ativo, assumindo todos ativos
    
    $total_produtos = $crudProduto->count();
    $produtos_disponiveis = $crudProduto->countByStatus('Disponivel');
    
    $total_pedidos = $crudPedido->count();
    $pedidos_hoje = $crudPedido->countToday();
    
    $total_funcionarios = $crudFuncionario->count();
    $funcionarios_ativos = $crudFuncionario->countActive();
    
    $total_categorias = $crudCategoria->count();
    
    // Estatísticas de receita
    $receita_total = $crudPedido->getReceitaTotal();
    $receita_mes = $crudPedido->getReceitaMes();
    $receita_hoje = $crudPedido->getReceitaHoje();
    
    // Pedidos recentes
    $pedidos_recentes = $crudPedido->readAll('', '', 1, 5);
    
    // Produtos mais vendidos
    $produtos_populares = $crudProduto->getMaisVendidos(5);
    
    // Estatísticas por status
    $pedidos_por_status_raw = $crudPedido->getCountByStatus();
    
    // Converter para formato associativo
    $pedidos_por_status = [];
    foreach ($pedidos_por_status_raw as $item) {
        $pedidos_por_status[$item['status_pedido']] = $item['total'];
    }
    
    // Organizar estatísticas de pedidos para o dashboard
    $estatisticas_pedidos = [
        'status' => $pedidos_por_status,
        'hoje' => $pedidos_hoje
    ];

} catch (Exception $e) {
    $error = "Erro ao carregar dados: " . $e->getMessage();
    // Valores padrão
    $total_usuarios = $usuarios_ativos = 0;
    $total_produtos = $produtos_disponiveis = 0;
    $total_pedidos = $pedidos_hoje = 0;
    $total_funcionarios = $funcionarios_ativos = 0;
    $total_categorias = 0;
    $receita_total = $receita_mes = $receita_hoje = 0;
    $pedidos_recentes = [];
    $produtos_populares = [];
    $pedidos_por_status = [];
    $estatisticas_pedidos = ['status' => [], 'hoje' => 0];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecoute Saveur - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="container">
            <div class="page-header">
                <div class="page-title">
                    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                    <p>Bem-vindo ao painel administrativo do Ecoute Saveur</p>
                </div>
                <div class="page-actions">
                    <span class="current-time">
                        <i class="fas fa-clock"></i>
                        <?= date('d/m/Y H:i') ?>
                    </span>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Estatísticas Cards Principais -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_usuarios) ?></div>
                        <div class="stat-label">Usuários Totais</div>
                        <div class="stat-detail">
                            <span class="stat-highlight"><?= $usuarios_ativos ?></span> ativos
                        </div>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_produtos) ?></div>
                        <div class="stat-label">Produtos</div>
                        <div class="stat-detail">
                            <span class="stat-highlight"><?= $produtos_disponiveis ?></span> disponíveis
                        </div>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_pedidos) ?></div>
                        <div class="stat-label">Pedidos Totais</div>
                        <div class="stat-detail">
                            <span class="stat-highlight"><?= $pedidos_hoje ?></span> hoje
                        </div>
                    </div>
                </div>

                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_funcionarios) ?></div>
                        <div class="stat-label">Funcionários</div>
                        <div class="stat-detail">
                            <span class="stat-highlight"><?= $funcionarios_ativos ?></span> ativos
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas de Receita -->
            <div class="revenue-grid">
                <div class="revenue-card today">
                    <div class="revenue-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="revenue-content">
                        <div class="revenue-amount">R$ <?= number_format($receita_hoje, 2, ',', '.') ?></div>
                        <div class="revenue-label">Hoje</div>
                    </div>
                </div>

                <div class="revenue-card month">
                    <div class="revenue-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="revenue-content">
                        <div class="revenue-amount">R$ <?= number_format($receita_mes, 2, ',', '.') ?></div>
                        <div class="revenue-label">Este Mês</div>
                    </div>
                </div>

                <div class="revenue-card total">
                    <div class="revenue-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="revenue-content">
                        <div class="revenue-amount">R$ <?= number_format($receita_total, 2, ',', '.') ?></div>
                        <div class="revenue-label">Total</div>
                    </div>
                </div>
            </div>

            <!-- Seção Principal com Duas Colunas -->
            <div class="row">
                <!-- Pedidos Recentes -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-shopping-bag"></i> Pedidos Recentes</h3>
                            <a href="pedidos/index.php" class="btn btn-sm btn-primary">Ver Todos</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pedidos_recentes)): ?>
                                <div class="recent-orders">
                                    <?php foreach ($pedidos_recentes as $pedido): ?>
                                    <div class="order-item">
                                        <div class="order-info">
                                            <div class="order-header">
                                                <span class="order-id">#<?= $pedido['id_pedido'] ?></span>
                                                <span class="order-time"><?= date('H:i', strtotime($pedido['data_pedido'])) ?></span>
                                            </div>
                                            <div class="order-customer">
                                                <i class="fas fa-user"></i>
                                                <?= htmlspecialchars($pedido['primeiro_nome'] . ' ' . $pedido['segundo_nome']) ?>
                                            </div>
                                        </div>
                                        <div class="order-status">
                                            <span class="status-badge status-<?= strtolower(str_replace([' ', 'ã', 'á'], ['', 'a', 'a'], $pedido['status_pedido'])) ?>">
                                                <?= $pedido['status_pedido'] ?>
                                            </span>
                                        </div>
                                        <div class="order-total">
                                            R$ <?= number_format($pedido['total_pedido'] ?? 0, 2, ',', '.') ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-shopping-bag"></i>
                                    <p>Nenhum pedido encontrado</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Status dos Pedidos & Produtos Populares -->
                <div class="col-md-4">
                    <!-- Status dos Pedidos -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-pie"></i> Status dos Pedidos</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pedidos_por_status)): ?>
                                <div class="status-chart">
                                    <?php foreach ($pedidos_por_status as $status_nome => $total): ?>
                                    <div class="status-item">
                                        <div class="status-info">
                                            <span class="status-badge status-<?= strtolower(str_replace([' ', 'ã', 'á'], ['', 'a', 'a'], $status_nome)) ?>">
                                                <?= $status_nome ?>
                                            </span>
                                            <span class="status-count"><?= $total ?></span>
                                        </div>
                                        <div class="status-bar">
                                            <?php 
                                            $percentage = $total_pedidos > 0 ? ($total / $total_pedidos) * 100 : 0;
                                            ?>
                                            <div class="status-progress" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-chart-pie"></i>
                                    <p>Nenhum dado disponível</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Produtos Populares -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-star"></i> Produtos Populares</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($produtos_populares)): ?>
                                <div class="popular-products">
                                    <?php foreach ($produtos_populares as $index => $produto): ?>
                                    <div class="product-item">
                                        <div class="product-rank"><?= $index + 1 ?></div>
                                        <div class="product-info">
                                            <div class="product-name"><?= htmlspecialchars($produto['nome_produto']) ?></div>
                                            <div class="product-sales">
                                                <i class="fas fa-shopping-cart"></i>
                                                <?= $produto['total_vendido'] ?> vendidos
                                            </div>
                                        </div>
                                        <div class="product-price">
                                            R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-star"></i>
                                    <p>Nenhum produto vendido</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
                        </div>
                        <div class="card-body">
                            <div class="status-stats">
                                <?php if (!empty($estatisticas_pedidos['status'])): ?>
                                    <?php foreach ($estatisticas_pedidos['status'] as $status => $count): ?>
                                    <div class="status-item">
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '', $status)) ?>">
                                            <?= $status ?>
                                        </span>
                                        <span class="status-count"><?= $count ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Nenhum pedido encontrado</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Resumo Hoje</h3>
                        </div>
                        <div class="card-body">
                            <div class="today-stats">
                                <div class="today-item">
                                    <i class="fas fa-shopping-bag"></i>
                                    <div>
                                        <h4><?= $estatisticas_pedidos['hoje'] ?? 0 ?></h4>
                                        <p>Pedidos Hoje</p>
                                    </div>
                                </div>
                                <div class="today-item">
                </div>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>

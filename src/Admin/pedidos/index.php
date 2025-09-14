<?php
session_start();

// Debug - mostrar erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Permitir acesso direto
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

require_once __DIR__ . '/../../db/conection.php';
require_once __DIR__ . '/../../controllers/pedido/Crud_pedido.php';

$pageTitle = "Gerenciar Pedidos";
$erro = null;
$pedidos = [];
$total_geral = 0;
$pedidos_hoje = 0;
$pedidos_pendentes = 0;
$pedidos_concluidos = 0;
$receita_total = 0;
$receita_hoje = 0;

// Parâmetros de busca e paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

try {
    // Criar conexão
    $database = new Database();
    $conexao = $database->getInstance();
    $crudPedido = new Crud_pedido($conexao);
    
    // Buscar dados
    $total_geral = $crudPedido->count();
    $pedidos_hoje = $crudPedido->countToday();
    $pedidos_pendentes = $crudPedido->countByStatus('Pendente');
    $pedidos_concluidos = $crudPedido->countByStatus('Concluido');
    
    // Buscar receitas (se métodos existirem)
    if (method_exists($crudPedido, 'getReceitaTotal')) {
        $receita_total = $crudPedido->getReceitaTotal();
    }
    if (method_exists($crudPedido, 'getReceitaHoje')) {
        $receita_hoje = $crudPedido->getReceitaHoje();
    }
    
    // Buscar pedidos
    $pedidos = $crudPedido->readAll($search, $status_filter, $page, $perPage);
    $total_pedidos = $crudPedido->count($search, $status_filter);
    $total_pages = ceil($total_pedidos / $perPage);
    
} catch (Exception $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .stats-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .stats-card.primary { border-left-color: #0d6efd; }
        .stats-card.success { border-left-color: #198754; }
        .stats-card.warning { border-left-color: #ffc107; }
        .stats-card.danger { border-left-color: #dc3545; }
        .stats-card.info { border-left-color: #0dcaf0; }
        
        .table-actions {
            white-space: nowrap;
        }
        
        .badge-status {
            font-size: 0.8em;
        }
        
        .pedido-valor {
            font-weight: bold;
            color: #198754;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="container-fluid">
            <!-- Header da página -->
            <div class="page-header mb-4">
                <div class="page-title">
                    <h1><i class="fas fa-shopping-bag"></i> <?= $pageTitle ?></h1>
                    <p>Gerencie todos os pedidos do restaurante</p>
                </div>
            </div>

            <?php if ($erro): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Cards de Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Total de Pedidos</h6>
                                    <h2 class="mb-0"><?= number_format($total_geral) ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Pedidos Hoje</h6>
                                    <h2 class="mb-0"><?= number_format($pedidos_hoje) ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-day fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Pendentes</h6>
                                    <h2 class="mb-0"><?= number_format($pedidos_pendentes) ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Concluídos</h6>
                                    <h2 class="mb-0"><?= number_format($pedidos_concluidos) ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de Receita -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card stats-card info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Receita Total</h6>
                                    <h2 class="mb-0">R$ <?= number_format($receita_total, 2, ',', '.') ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card stats-card success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Receita Hoje</h6>
                                    <h2 class="mb-0">R$ <?= number_format($receita_hoje, 2, ',', '.') ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros e Busca -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Buscar pedidos</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="ID do pedido, cliente..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Filtrar por status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos os status</option>
                                <option value="Pendente" <?= $status_filter === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="Processando" <?= $status_filter === 'Processando' ? 'selected' : '' ?>>Processando</option>
                                <option value="A caminho" <?= $status_filter === 'A caminho' ? 'selected' : '' ?>>A caminho</option>
                                <option value="Concluido" <?= $status_filter === 'Concluido' ? 'selected' : '' ?>>Concluído</option>
                                <option value="Cancelado" <?= $status_filter === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela de Pedidos -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Lista de Pedidos 
                        <span class="badge bg-secondary"><?= count($pedidos) ?> pedidos</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($pedidos)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Data/Hora</th>
                                    <th>Status</th>
                                    <th>Valor Total</th>
                                    <th>Endereço</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><strong>#<?= $pedido['id_pedido'] ?></strong></td>
                                    <td>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars(($pedido['primeiro_nome'] ?? '') . ' ' . ($pedido['segundo_nome'] ?? '')) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($pedido['email'] ?? '') ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($pedido['data_pedido'])) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = 'secondary';
                                        switch($pedido['status_pedido']) {
                                            case 'Pendente': $badge_class = 'warning'; break;
                                            case 'Processando': $badge_class = 'info'; break;
                                            case 'A caminho': $badge_class = 'primary'; break;
                                            case 'Concluido': $badge_class = 'success'; break;
                                            case 'Cancelado': $badge_class = 'danger'; break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $badge_class ?> badge-status">
                                            <?= htmlspecialchars($pedido['status_pedido']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="pedido-valor">
                                            R$ <?= number_format($pedido['total_pedido'] ?? 0, 2, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($pedido['endereco'] ?? 'N/A') ?></small>
                                    </td>
                                    <td class="text-center table-actions">
                                        <button class="btn btn-sm btn-outline-info me-1" 
                                                onclick="verDetalhes(<?= $pedido['id_pedido'] ?>)"
                                                title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                onclick="alterarStatus(<?= $pedido['id_pedido'] ?>, '<?= $pedido['status_pedido'] ?>')"
                                                title="Alterar status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmarExclusao(<?= $pedido['id_pedido'] ?>, '<?= $pedido['id_pedido'] ?>')"
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Paginação" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>

                    <?php else: ?>
                    <div class="text-center p-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum pedido encontrado</h5>
                        <p class="text-muted">
                            <?php if (empty($search) && empty($status_filter)): ?>
                                Não há pedidos cadastrados no sistema. 
                                <a href="criar_dados_teste.php" class="btn btn-link">Criar dados de teste</a>
                            <?php else: ?>
                                Não há pedidos que correspondam aos filtros aplicados.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para alterar status -->
    <div class="modal fade" id="modalAlterarStatus" tabindex="-1" aria-labelledby="modalAlterarStatusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAlterarStatusLabel">Alterar Status do Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAlterarStatus">
                        <input type="hidden" id="idPedido" name="id_pedido">
                        
                        <div class="mb-3">
                            <label for="novoStatus" class="form-label">Novo Status:</label>
                            <select class="form-select" id="novoStatus" name="status" required>
                                <option value="Pendente">Pendente</option>
                                <option value="Processando">Processando</option>
                                <option value="A caminho">A caminho</option>
                                <option value="Concluido">Concluído</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações (opcional):</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3" 
                                      placeholder="Digite observações sobre a mudança de status..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarAlteracaoStatus()">
                        <i class="fas fa-save me-1"></i>Alterar Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verDetalhes(idPedido) {
            alert('Funcionalidade de detalhes será implementada. Pedido: ' + idPedido);
        }

        function alterarStatus(idPedido, statusAtual) {
            // Preencher o modal com os dados
            document.getElementById('idPedido').value = idPedido;
            document.getElementById('novoStatus').value = statusAtual;
            document.getElementById('observacoes').value = '';
            
            // Atualizar o título do modal
            document.getElementById('modalAlterarStatusLabel').textContent = `Alterar Status do Pedido #${idPedido}`;
            
            // Mostrar o modal
            const modal = new bootstrap.Modal(document.getElementById('modalAlterarStatus'));
            modal.show();
        }

        function confirmarAlteracaoStatus() {
            const formData = new FormData(document.getElementById('formAlterarStatus'));
            const idPedido = formData.get('id_pedido');
            const novoStatus = formData.get('status');
            
            // Mostrar loading
            const btnConfirmar = document.querySelector('#modalAlterarStatus .btn-primary');
            const textoOriginal = btnConfirmar.innerHTML;
            btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Alterando...';
            btnConfirmar.disabled = true;

            // Enviar via AJAX
            fetch('update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar sucesso
                    alert('Status alterado com sucesso!');
                    
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalAlterarStatus'));
                    modal.hide();
                    
                    // Recarregar a página para mostrar a mudança
                    window.location.reload();
                } else {
                    alert('Erro ao alterar status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro de conexão. Tente novamente.');
            })
            .finally(() => {
                // Restaurar botão
                btnConfirmar.innerHTML = textoOriginal;
                btnConfirmar.disabled = false;
            });
        }

        function confirmarExclusao(idPedido, identificacao) {
            if (confirm('Tem certeza que deseja excluir o pedido #' + identificacao + '?\n\nEsta ação não pode ser desfeita!')) {
                // Aqui implementaríamos a exclusão via AJAX
                alert('Pedido excluído!\n(Funcionalidade será implementada)');
            }
        }
    </script>
</body>
</html>

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
require_once __DIR__ . '/../../controllers/avaliacao/Crud_avaliacao.php';

$pageTitle = "Gerenciar Avaliações";
$erro = null;
$avaliacoes = [];
$total_geral = 0;
$avaliacoes_hoje = 0;
$avaliacoes_pendentes = 0;
$avaliacoes_aprovadas = 0;
$media_avaliacoes = 0;

// Parâmetros de busca e paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Processar ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $conexao = $database->getInstance();
        $crudAvaliacao = new Crud_avaliacao($conexao);
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'responder':
                    if (isset($_POST['id']) && isset($_POST['resposta'])) {
                        $crudAvaliacao->responder($_POST['id'], $_POST['resposta']);
                        $success = "Resposta enviada com sucesso!";
                    }
                    break;
                    
                case 'update_status':
                    if (isset($_POST['id']) && isset($_POST['status'])) {
                        $crudAvaliacao->setStatus($_POST['status']);
                        $crudAvaliacao->update($_POST['id']);
                        $success = "Status atualizado com sucesso!";
                    }
                    break;
                    
                case 'delete':
                    if (isset($_POST['id'])) {
                        $crudAvaliacao->delete($_POST['id']);
                        $success = "Avaliação excluída com sucesso!";
                    }
                    break;
            }
        }
        
        // Redirecionar para evitar resubmissão
        if (isset($success)) {
            header('Location: index.php?success=' . urlencode($success));
            exit;
        }
    } catch (Exception $e) {
        $erro = "Erro ao processar ação: " . $e->getMessage();
    }
}

try {
    // Criar conexão
    $database = new Database();
    $conexao = $database->getInstance();
    $crudAvaliacao = new Crud_avaliacao($conexao);
    
    // Buscar dados
    $total_geral = $crudAvaliacao->count();
    $avaliacoes_hoje = $crudAvaliacao->countToday();
    $avaliacoes_pendentes = $crudAvaliacao->countByStatus('Pendente');
    $avaliacoes_aprovadas = $crudAvaliacao->countByStatus('Aprovada');
    $media_avaliacoes = $crudAvaliacao->getMediaAvaliacoes();
    
    // Buscar avaliações
    $avaliacoes = $crudAvaliacao->readAll($search, $status_filter, $page, $perPage);
    $total_avaliacoes = $crudAvaliacao->count($search, $status_filter);
    $total_pages = ceil($total_avaliacoes / $perPage);
    
} catch (Exception $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}

// Mensagem de sucesso da URL
$success = isset($_GET['success']) ? $_GET['success'] : null;
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
        
        .rating-stars {
            color: #ffc107;
        }
        
        .avaliacao-texto {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
                    <h1><i class="fas fa-star"></i> <?= $pageTitle ?></h1>
                    <p>Gerencie todas as avaliações dos clientes</p>
                </div>
            </div>

            <?php if ($erro): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
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
                                    <h6 class="card-title text-muted mb-2">Total de Avaliações</h6>
                                    <h2 class="mb-0"><?= number_format($total_geral) ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-star fa-2x text-primary"></i>
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
                                    <h6 class="card-title text-muted mb-2">Avaliações Hoje</h6>
                                    <h2 class="mb-0"><?= number_format($avaliacoes_hoje) ?></h2>
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
                                    <h2 class="mb-0"><?= number_format($avaliacoes_pendentes) ?></h2>
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
                                    <h6 class="card-title text-muted mb-2">Média Geral</h6>
                                    <h2 class="mb-0"><?= number_format($media_avaliacoes, 1) ?> <i class="fas fa-star text-warning"></i></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x text-success"></i>
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
                            <label for="search" class="form-label">Buscar avaliações</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Cliente, texto da avaliação..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Filtrar por status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos os status</option>
                                <option value="Pendente" <?= $status_filter === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="Aprovada" <?= $status_filter === 'Aprovada' ? 'selected' : '' ?>>Aprovada</option>
                                <option value="Rejeitada" <?= $status_filter === 'Rejeitada' ? 'selected' : '' ?>>Rejeitada</option>
                                <option value="Respondida" <?= $status_filter === 'Respondida' ? 'selected' : '' ?>>Respondida</option>
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

            <!-- Tabela de Avaliações -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Lista de Avaliações 
                        <span class="badge bg-secondary"><?= count($avaliacoes) ?> avaliações</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($avaliacoes)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Nota</th>
                                    <th>Avaliação</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Pedido</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($avaliacoes as $avaliacao): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars(($avaliacao['primeiro_nome'] ?? '') . ' ' . ($avaliacao['segundo_nome'] ?? '')) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($avaliacao['email'] ?? '') ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= $avaliacao['nota'] ? '' : '-o' ?>"></i>
                                            <?php endfor; ?>
                                            <small class="text-muted">(<?= $avaliacao['nota'] ?>/5)</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="avaliacao-texto" title="<?= htmlspecialchars($avaliacao['texto_avaliacao']) ?>">
                                            <?= htmlspecialchars($avaliacao['texto_avaliacao']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?= date('d/m/Y', strtotime($avaliacao['data_avaliacao'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($avaliacao['data_avaliacao'])) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = 'secondary';
                                        switch($avaliacao['status']) {
                                            case 'Pendente': $badge_class = 'warning'; break;
                                            case 'Aprovada': $badge_class = 'success'; break;
                                            case 'Rejeitada': $badge_class = 'danger'; break;
                                            case 'Respondida': $badge_class = 'info'; break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $badge_class ?>">
                                            <?= htmlspecialchars($avaliacao['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= $avaliacao['id_pedido'] ? '#' . $avaliacao['id_pedido'] : 'N/A' ?></small>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info me-1" 
                                                onclick="verDetalhes(<?= $avaliacao['id_avaliacao'] ?>)"
                                                title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                onclick="responderAvaliacao(<?= $avaliacao['id_avaliacao'] ?>)"
                                                title="Responder">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning me-1" 
                                                onclick="alterarStatus(<?= $avaliacao['id_avaliacao'] ?>, '<?= $avaliacao['status'] ?>')"
                                                title="Alterar status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmarExclusao(<?= $avaliacao['id_avaliacao'] ?>)"
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
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma avaliação encontrada</h5>
                        <p class="text-muted">
                            <?php if (empty($search) && empty($status_filter)): ?>
                                Não há avaliações cadastradas no sistema.
                            <?php else: ?>
                                Não há avaliações que correspondam aos filtros aplicados.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verDetalhes(idAvaliacao) {
            alert('Funcionalidade de detalhes será implementada. Avaliação: ' + idAvaliacao);
        }

        function responderAvaliacao(idAvaliacao) {
            const resposta = prompt('Digite sua resposta para esta avaliação:');
            if (resposta && resposta.trim() !== '') {
                // Criar formulário para enviar resposta
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="responder">
                    <input type="hidden" name="id" value="${idAvaliacao}">
                    <input type="hidden" name="resposta" value="${resposta}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function alterarStatus(idAvaliacao, statusAtual) {
            const novoStatus = prompt('Digite o novo status para a avaliação:\n\nOpções: Pendente, Aprovada, Rejeitada, Respondida', statusAtual);
            if (novoStatus && novoStatus !== statusAtual) {
                // Criar formulário para enviar alteração
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="${idAvaliacao}">
                    <input type="hidden" name="status" value="${novoStatus}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function confirmarExclusao(idAvaliacao) {
            if (confirm('Tem certeza que deseja excluir esta avaliação?\n\nEsta ação não pode ser desfeita!')) {
                // Criar formulário para enviar exclusão
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${idAvaliacao}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto-dismiss alerts após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 5000);
    </script>
</body>
</html>

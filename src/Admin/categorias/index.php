<?php
session_start();
require_once '../controllers/categoria/Crud_categoria.php';

// Permitir acesso direto - criar sessão admin automática se não existir
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Criar sessão admin automática
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

// Instanciar classe CRUD
$crudCategoria = new Crud_categoria();

// Processar ações
$message = '';
$message_type = '';

if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete' && isset($_POST['id'])) {
        try {
            $crudCategoria->delete($_POST['id']);
            $message = 'Categoria excluída com sucesso!';
            $message_type = 'success';
        } catch (Exception $e) {
            $message = 'Erro ao excluir categoria: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Buscar categorias
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 12;

try {
    $categorias = $crudCategoria->readAll($search, $page, $perPage);
    $total_records = $crudCategoria->count($search);
    $total_pages = ceil($total_records / $perPage);
    
    // Estatísticas
    $total_categorias = $crudCategoria->count();

} catch (Exception $e) {
    $error = "Erro ao carregar categorias: " . $e->getMessage();
    $categorias = [];
    $total_records = 0;
    $total_pages = 0;
    $total_categorias = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Ecoute Saveur Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="container">
            <div class="page-header">
                <div class="page-title">
                    <h1><i class="fas fa-tags"></i> Categorias</h1>
                    <p>Gerencie as categorias dos produtos</p>
                </div>
                <div class="page-actions">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nova Categoria
                    </a>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_categorias) ?></div>
                        <div class="stat-label">Total de Categorias</div>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="filters-row">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Buscar categorias..." 
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($categorias)): ?>
                        <div class="categories-grid">
                            <?php foreach ($categorias as $categoria): ?>
                            <div class="category-card">
                                <div class="category-header">
                                    <h3><?= htmlspecialchars($categoria['nome_categoria']) ?></h3>
                                    <div class="category-id">#<?= $categoria['id_categoria'] ?></div>
                                </div>
                                <div class="category-description">
                                    <?= htmlspecialchars($categoria['descricao'] ?? 'Sem descrição') ?>
                                </div>
                                <div class="category-actions">
                                    <a href="view.php?id=<?= $categoria['id_categoria'] ?>" class="btn btn-sm btn-info" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $categoria['id_categoria'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $categoria['id_categoria'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-tags"></i>
                            <p>Nenhuma categoria encontrada</p>
                            <a href="create.php" class="btn btn-primary">Criar primeira categoria</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Mostrando <?= count($categorias) ?> de <?= $total_records ?> registros
                    </div>
                    <div class="pagination-links">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
                               class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        function confirmDelete() {
            return confirm('Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita.');
        }

        function searchCategories() {
            const search = document.getElementById('searchInput').value;
            window.location.href = `?search=${encodeURIComponent(search)}`;
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchCategories();
            }
        });
    </script>
</body>
</html>

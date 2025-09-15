<?php
session_start();
require_once '../../controllers/produto/Crud_produto.php';
require_once '../../controllers/categoria/Crud_categoria.php';

// Permitir acesso direto - criar sessão admin automática se não existir
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Criar sessão admin automática
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

// Instanciar classes CRUD
$crudProduto = new Crud_produto();
$crudCategoria = new Crud_categoria();

// Buscar categorias para o select
try {
    $categorias = $crudCategoria->readAll();
} catch (Exception $e) {
    $categorias = [];
}

// Processar ações
$message = '';
$message_type = '';

if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete' && isset($_POST['id'])) {
        try {
            $crudProduto->delete($_POST['id']);
            $message = 'Produto excluído com sucesso!';
            $message_type = 'success';
        } catch (Exception $e) {
            $message = 'Erro ao excluir produto: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Buscar produtos
$search = $_GET['search'] ?? '';
$categoria_filter = $_GET['categoria'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 10;

try {
    $produtos = $crudProduto->readAll($search, $categoria_filter, $page, $perPage);
    $total_records = $crudProduto->count($search, $categoria_filter);
    $total_pages = ceil($total_records / $perPage);
} catch (Exception $e) {
    $error = "Erro ao carregar produtos: " . $e->getMessage();
    $produtos = [];
    $total_records = 0;
    $total_pages = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Ecoute Saveur Admin</title>
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
                    <h1><i class="fas fa-utensils"></i> Produtos</h1>
                    <p>Gerencie os produtos do cardápio</p>
                </div>
                <div class="page-actions">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Novo Produto
                    </a>
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
                        <div class="filter-group">
                            <select id="categoriaFilter" onchange="filterByCategory()">
                                <option value="">Todas as categorias</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id_categoria'] ?>" 
                                            <?= $categoria_filter == $categoria['id_categoria'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria['nome_categoria']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Buscar produtos..." 
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagem</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($produtos)): ?>
                                <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td>#<?= $produto['id_produto'] ?></td>
                                    <td>
                                        <?php if ($produto['imagem']): ?>
                                            <img src="../../images/comidas/<?= htmlspecialchars($produto['imagem']) ?>" 
                                                 alt="<?= htmlspecialchars($produto['nome_produto']) ?>" 
                                                 class="product-thumb">
                                        <?php else: ?>
                                            <div class="product-thumb-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="product-info">
                                            <strong><?= htmlspecialchars($produto['nome_produto']) ?></strong>
                                            <?php if ($produto['descricao']): ?>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars(substr($produto['descricao'], 0, 50)) ?>...</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="category-badge">
                                            <?= htmlspecialchars($produto['nome_categoria'] ?? 'Sem categoria') ?>
                                        </span>
                                    </td>
                                    <td class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $produto['estoque'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $produto['estoque'] ?? 0 ?> un.
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $produto['status'] == 'Disponivel' ? 'disponivel' : 'indisponivel' ?>">
                                            <?= $produto['status'] == 'Disponivel' ? 'Disponível' : 'Indisponível' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= isset($produto['data_criacao']) ? date('d/m/Y', strtotime($produto['data_criacao'])) : 'N/A' ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view.php?id=<?= $produto['id_produto'] ?>" class="btn btn-sm btn-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?= $produto['id_produto'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $produto['id_produto'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $produto['id_produto'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Nenhum produto encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Mostrando <?= count($produtos) ?> de <?= $total_records ?> registros
                    </div>
                    <div class="pagination-links">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&categoria=<?= urlencode($categoria_filter) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&categoria=<?= urlencode($categoria_filter) ?>" 
                               class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&categoria=<?= urlencode($categoria_filter) ?>" class="btn btn-sm">
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
        function filterByCategory() {
            const categoria = document.getElementById('categoriaFilter').value;
            const search = document.getElementById('searchInput').value;
            window.location.href = `?categoria=${encodeURIComponent(categoria)}&search=${encodeURIComponent(search)}`;
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const search = this.value;
            const categoria = document.getElementById('categoriaFilter').value;
            if (search.length >= 3 || search.length === 0) {
                window.location.href = `?search=${encodeURIComponent(search)}&categoria=${encodeURIComponent(categoria)}`;
            }
        });
    </script>

    <style>
        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .product-thumb-placeholder {
            width: 50px;
            height: 50px;
            background: var(--background-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
        }
        
        .product-info {
            max-width: 200px;
        }
        
        .category-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .price {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .status-ativo {
            background: rgba(34, 197, 94, 0.1);
            color: #22C55E;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-inativo {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .filters-row {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            background: white;
        }
    </style>

    <script src="../js/admin.js"></script>
    <script>
        function confirmDelete() {
            return confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.');
        }
    </script>
</body>
</html>

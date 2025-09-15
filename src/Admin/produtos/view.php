<?php
session_start();
require_once '../../models/Crud_produto.php';
require_once '../../models/Crud_categoria.php';
require_once '../../helpers/image_helper.php';

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Criar sessão admin automática
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

// Instanciar classes CRUD
$crudProduto = new Crud_produto();
$crudCategoria = new Crud_categoria();

try {
    $produto = $crudProduto->readById($id);
    if (!$produto) {
        header('Location: index.php');
        exit();
    }
    
    $categorias = $crudCategoria->readAll();
    
} catch (Exception $e) {
    $error = "Erro ao carregar produto: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($produto['nome_produto']) ?> - Ecoute Saveur Admin</title>
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
                    <h1><i class="fas fa-utensils"></i> <?= htmlspecialchars($produto['nome_produto']) ?></h1>
                    <p>Visualizar detalhes do produto</p>
                </div>
                <div class="page-actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar
                    </a>
                    <a href="edit.php?id=<?= $produto['id_produto'] ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Editar
                    </a>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Imagem do Produto -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-image"></i> Imagem</h3>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($produto['imagem']): ?>
                                <img src="<?= getImageSrcAdmin($produto['imagem']) ?>" 
                                     alt="<?= htmlspecialchars($produto['nome_produto']) ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                    <p>Sem imagem</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Informações do Produto -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-info-circle"></i> Informações do Produto</h3>
                        </div>
                        <div class="card-body">
                            <div class="product-details">
                                <div class="product-detail-row">
                                    <span class="product-detail-label">Nome:</span>
                                    <span class="product-detail-value"><?= htmlspecialchars($produto['nome_produto']) ?></span>
                                </div>
                                
                                <div class="product-detail-row">
                                    <span class="product-detail-label">Categoria:</span>
                                    <span class="category-badge">
                                        <?= htmlspecialchars($produto['nome_categoria'] ?? 'Sem categoria') ?>
                                    </span>
                                </div>
                                
                                <div class="product-detail-row">
                                    <span class="product-detail-label">Preço:</span>
                                    <span class="product-detail-value price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                                </div>
                                
                                <div class="product-detail-row">
                                    <span class="product-detail-label">Status:</span>
                                    <span class="status-badge status-<?= $produto['status'] == 'Disponivel' ? 'disponivel' : 'indisponivel' ?>">
                                        <?= $produto['status'] == 'Disponivel' ? 'Disponível' : 'Indisponível' ?>
                                    </span>
                                </div>
                                
                                <div class="product-detail-row">
                                    <span class="product-detail-label">Estoque:</span>
                                    <span class="product-detail-value"><?= $produto['estoque'] ?? '0' ?> unidades</span>
                                </div>
                                
                                <div class="product-detail-row">
                                    <span class="product-detail-label">Data de Criação:</span>
                                    <span class="product-detail-value"><?= date('d/m/Y H:i', strtotime($produto['data_criacao'])) ?></span>
                                </div>
                                
                                <?php if ($produto['data_atualizacao']): ?>
                                <div class="product-detail-row">
                                    <span class="product-detail-label">Última Atualização:</span>
                                    <span class="product-detail-value"><?= date('d/m/Y H:i', strtotime($produto['data_atualizacao'])) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descrição -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-align-left"></i> Descrição</h3>
                </div>
                <div class="card-body">
                    <div class="description">
                        <?php if ($produto['descricao']): ?>
                            <p class="product-detail-value description"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                        <?php else: ?>
                            <p class="text-muted">Nenhuma descrição disponível.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ingredientes -->
            <?php if (isset($produto['ingredientes']) && $produto['ingredientes']): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Ingredientes</h3>
                </div>
                <div class="card-body">
                    <div class="ingredients">
                        <p><?= nl2br(htmlspecialchars($produto['ingredientes'])) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/admin.js"></script>
</body>
</html>
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .no-image {
            background: var(--background-light);
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 40px 20px;
            color: var(--text-muted);
        }
        
        .no-image i {
            font-size: 48px;
            margin-bottom: 12px;
        }
        
        .product-details .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .product-details .detail-row:last-child {
            border-bottom: none;
        }
        
        .product-details label {
            font-weight: 600;
            color: var(--text-dark);
            min-width: 140px;
        }
        
        .category-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .price {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-ativo {
            background: rgba(34, 197, 94, 0.1);
            color: #22C55E;
        }
        
        .status-inativo {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
        }
        
        .col-md-4 {
            width: 32%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
        }
        
        .col-md-8 {
            width: 65%;
            display: inline-block;
            vertical-align: top;
        }
        
    <script src="../js/admin.js"></script>
</body>
</html>

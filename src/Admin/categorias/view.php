<?php
session_start();
require_once '../../controllers/categoria/Crud_categoria.php';
require_once '../../controllers/produto/Crud_produto.php';

// Criar sessão admin automática se não existir
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
}

if (!isset($_GET['id'])) {
    $_SESSION['message'] = 'ID da categoria não fornecido!';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

$crudCategoria = new Crud_categoria();
$crudProduto = new Crud_produto();

$categoria = $crudCategoria->readById($_GET['id']);
if (!$categoria) {
    $_SESSION['message'] = 'Categoria não encontrada!';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Buscar produtos desta categoria
$produtos = $crudProduto->readByCategoria($_GET['id']);

// Mensagens de sessão
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($categoria['nome_categoria']) ?> - Ecoute Saveur Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .category-info-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 30px;
            margin-bottom: 30px;
        }

        .category-main {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .category-sidebar {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .category-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .category-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .category-description {
            font-size: 16px;
            line-height: 1.6;
            color: #666;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-label {
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .products-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .section-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .product-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .product-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: #f8f9fa;
        }

        .product-content {
            padding: 15px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 0 0 8px 0;
        }

        .product-price {
            font-size: 18px;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 8px;
        }

        .product-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .empty-products {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-products i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .category-info-grid {
                grid-template-columns: 1fr;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="container">
            <div class="page-header">
                <div class="page-title">
                    <h1><i class="fas fa-tag"></i> Visualizar Categoria</h1>
                    <p>Detalhes e produtos da categoria</p>
                </div>
                <div class="page-actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar
                    </a>
                    <a href="edit.php?id=<?= $categoria['id_categoria'] ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i>
                        Editar
                    </a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="category-info-grid">
                <!-- Informações Principais -->
                <div class="category-main">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <?= htmlspecialchars($categoria['nome_categoria']) ?>
                    </div>

                    <?php if (!empty($categoria['descricao'])): ?>
                        <div class="category-description">
                            <i class="fas fa-quote-left" style="color: #007bff; margin-right: 10px;"></i>
                            <?= nl2br(htmlspecialchars($categoria['descricao'])) ?>
                        </div>
                    <?php endif; ?>

                    <div class="action-buttons">
                        <a href="../produtos/create.php?categoria=<?= $categoria['id_categoria'] ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Adicionar Produto
                        </a>
                        <a href="../produtos/index.php?categoria=<?= $categoria['id_categoria'] ?>" class="btn btn-info">
                            <i class="fas fa-list"></i>
                            Ver Todos os Produtos
                        </a>
                    </div>
                </div>

                <!-- Sidebar com Informações -->
                <div class="category-sidebar">
                    <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">
                        <i class="fas fa-info-circle"></i> Informações
                    </h3>

                    <div class="info-group">
                        <div class="info-label">ID da Categoria</div>
                        <div class="info-value">#<?= $categoria['id_categoria'] ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Total de Produtos</div>
                        <div class="info-value"><?= count($produtos) ?> produtos</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Produtos Ativos</div>
                        <div class="info-value"><?= count(array_filter($produtos, fn($p) => $p['ativo'])) ?> produtos</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Data de Criação</div>
                        <div class="info-value"><?= date('d/m/Y H:i', strtotime($categoria['created_at'] ?? 'now')) ?></div>
                    </div>

                    <?php if (isset($categoria['updated_at'])): ?>
                        <div class="info-group">
                            <div class="info-label">Última Atualização</div>
                            <div class="info-value"><?= date('d/m/Y H:i', strtotime($categoria['updated_at'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Seção de Produtos -->
            <div class="products-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-utensils"></i>
                        Produtos desta Categoria
                    </h2>
                </div>

                <?php if (empty($produtos)): ?>
                    <div class="empty-products">
                        <i class="fas fa-utensils"></i>
                        <h3>Nenhum produto encontrado</h3>
                        <p>Esta categoria ainda não possui produtos cadastrados.</p>
                        <a href="../produtos/create.php?categoria=<?= $categoria['id_categoria'] ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Adicionar Primeiro Produto
                        </a>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($produtos as $produto): ?>
                            <div class="product-card">
                                <?php if ($produto['imagem']): ?>
                                    <img src="../../images/comidas/<?= htmlspecialchars($produto['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($produto['nome_produto']) ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                        <i class="fas fa-image" style="font-size: 32px;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-content">
                                    <h4 class="product-name"><?= htmlspecialchars($produto['nome_produto']) ?></h4>
                                    <div class="product-price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                                    <span class="product-status <?= ($produto['status'] == 'Disponivel') ? 'status-active' : 'status-inactive' ?>">
                                        <?= ($produto['status'] == 'Disponivel') ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                    
                                    <div style="margin-top: 10px; display: flex; gap: 5px;">
                                        <a href="../produtos/view.php?id=<?= $produto['id_produto'] ?>" 
                                           class="btn btn-sm btn-info" style="flex: 1; text-align: center;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="../produtos/edit.php?id=<?= $produto['id_produto'] ?>" 
                                           class="btn btn-sm btn-warning" style="flex: 1; text-align: center;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

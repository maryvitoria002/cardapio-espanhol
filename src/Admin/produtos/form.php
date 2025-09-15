<?php
session_start();
require_once '../../models/Crud_produto.php';
require_once '../../models/Crud_categoria.php';
require_once '../../helpers/image_helper.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

$crudProduto = new Crud_produto();
$crudCategoria = new Crud_categoria();

$isEdit = isset($_GET['id']);
$produto = null;
$message = '';
$message_type = '';

if ($isEdit) {
    $produto = $crudProduto->readById($_GET['id']);
    if (!$produto) {
        $_SESSION['message'] = 'Produto não encontrado!';
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
        exit;
    }
}

$categorias = $crudCategoria->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome_produto = trim($_POST['nome_produto']);
        $descricao = trim($_POST['descricao']);
        $preco = floatval($_POST['preco']);
        $id_categoria = intval($_POST['id_categoria']);
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        
        // Validações
        if (empty($nome_produto)) {
            throw new Exception('Nome do produto é obrigatório');
        }
        
        if ($preco <= 0) {
            throw new Exception('Preço deve ser maior que zero');
        }
        
        if ($id_categoria <= 0) {
            throw new Exception('Categoria é obrigatória');
        }
        
        // Lidar com upload de imagem
        $imagem = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $crudProduto->uploadImagem($_FILES['imagem']);
            if ($uploadResult === false) {
                throw new Exception('Erro no upload da imagem');
            }
            $imagem = $uploadResult;
        }
        
        if ($isEdit) {
            // Atualizar produto
            $success = $crudProduto->update(
                $_GET['id'],
                $nome_produto,
                $descricao,
                $preco,
                $id_categoria,
                $imagem,
                $ativo
            );
            
            if ($success) {
                $message = 'Produto atualizado com sucesso!';
                $message_type = 'success';
                // Recarregar dados do produto
                $produto = $crudProduto->readById($_GET['id']);
            } else {
                throw new Exception('Erro ao atualizar produto');
            }
        } else {
            // Criar novo produto
            if (!$imagem) {
                throw new Exception('Imagem é obrigatória para novos produtos');
            }
            
            $id = $crudProduto->create($nome_produto, $descricao, $preco, $id_categoria, $imagem, $ativo);
            
            if ($id) {
                $_SESSION['message'] = 'Produto criado com sucesso!';
                $_SESSION['message_type'] = 'success';
                header('Location: view.php?id=' . $id);
                exit;
            } else {
                throw new Exception('Erro ao criar produto');
            }
        }
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Editar' : 'Novo' ?> Produto - Ecoute Saveur Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .image-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .image-upload {
            position: relative;
            margin-bottom: 15px;
        }

        .upload-placeholder {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px 20px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upload-placeholder:hover {
            border-color: #007bff;
            background: #f8f9ff;
        }

        .upload-placeholder i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .upload-placeholder p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .image-preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .product-form .row {
            display: flex;
            gap: 30px;
        }

        .product-form .col-md-4 {
            flex: 0 0 300px;
        }

        .product-form .col-md-8 {
            flex: 1;
        }

        .input-group {
            position: relative;
        }

        .input-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-weight: 500;
            z-index: 2;
        }

        .input-group .form-control {
            padding-left: 35px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            margin: 0;
            font-weight: 500;
        }

        .checkbox-label input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .btn-outline {
            background: white;
            border: 2px solid #007bff;
            color: #007bff;
        }

        .btn-outline:hover {
            background: #007bff;
            color: white;
        }

        .form-text {
            display: block;
            margin-top: 5px;
            color: #6c757d;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .product-form .row {
                flex-direction: column;
            }
            
            .product-form .col-md-4,
            .product-form .col-md-8 {
                flex: none;
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
                    <h1><i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i> <?= $isEdit ? 'Editar' : 'Novo' ?> Produto</h1>
                    <p><?= $isEdit ? 'Modificar informações do produto' : 'Adicionar novo produto ao cardápio' ?></p>
                </div>
                <div class="page-actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar
                    </a>
                    <?php if ($isEdit): ?>
                        <a href="view.php?id=<?= $produto['id_produto'] ?>" class="btn btn-info">
                            <i class="fas fa-eye"></i>
                            Visualizar
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-utensils"></i> Informações do Produto</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="product-form">
                        <div class="row">
                            <!-- Upload de Imagem -->
                            <div class="col-md-4">
                                <div class="image-section">
                                    <label>Imagem do Produto</label>
                                    <div class="image-upload">
                                        <?php if ($isEdit && $produto['imagem']): ?>
                                            <img src="<?= getImageSrcAdmin($produto['imagem']) ?>" 
                                                 alt="<?= htmlspecialchars($produto['nome_produto']) ?>" 
                                                 id="imagePreview" class="image-preview">
                                        <?php else: ?>
                                            <div class="upload-placeholder" id="uploadPlaceholder" onclick="document.getElementById('imagem').click()">
                                                <i class="fas fa-camera"></i>
                                                <p>Clique para adicionar imagem</p>
                                            </div>
                                            <img id="imagePreview" class="image-preview" style="display: none;">
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage(this)" style="display: none;">
                                        <button type="button" class="btn btn-outline" onclick="document.getElementById('imagem').click()">
                                            <i class="fas fa-upload"></i>
                                            <?= $isEdit ? 'Alterar Imagem' : 'Escolher Imagem' ?>
                                        </button>
                                        <small class="form-text">Formatos aceitos: JPG, PNG, GIF (máx. 5MB)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulário -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nome_produto">Nome do Produto *</label>
                                    <input type="text" id="nome_produto" name="nome_produto" class="form-control" 
                                           value="<?= htmlspecialchars($isEdit ? $produto['nome_produto'] : ($_POST['nome_produto'] ?? '')) ?>" 
                                           placeholder="Ex: Pizza Margherita" required>
                                </div>

                                <div class="form-group">
                                    <label for="id_categoria">Categoria *</label>
                                    <select id="id_categoria" name="id_categoria" class="form-control" required>
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id_categoria'] ?>" 
                                                    <?= ($isEdit ? $produto['id_categoria'] : ($_POST['id_categoria'] ?? '')) == $categoria['id_categoria'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria['nome_categoria']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="preco">Preço *</label>
                                    <div class="input-group">
                                        <span class="input-prefix">R$</span>
                                        <input type="number" id="preco" name="preco" class="form-control" 
                                               value="<?= $isEdit ? number_format($produto['preco'], 2, '.', '') : ($_POST['preco'] ?? '') ?>" 
                                               step="0.01" min="0" placeholder="0,00" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <textarea id="descricao" name="descricao" class="form-control" 
                                              rows="6" placeholder="Descreva o produto, ingredientes, etc..."><?= htmlspecialchars($isEdit ? $produto['descricao'] : ($_POST['descricao'] ?? '')) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <div class="checkbox-wrapper">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="ativo" 
                                                   <?= ($isEdit ? ($produto['status'] == 'Disponivel') : true) ? 'checked' : '' ?>>
                                            Produto ativo (visível no cardápio)
                                        </label>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        <?= $isEdit ? 'Salvar Alterações' : 'Criar Produto' ?>
                                    </button>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('uploadPlaceholder');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Validação do formulário
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.product-form');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            const nomeInput = document.getElementById('nome_produto');
            const categoriaSelect = document.getElementById('id_categoria');
            const precoInput = document.getElementById('preco');
            
            // Validações básicas
            if (!nomeInput.value.trim()) {
                e.preventDefault();
                alert('Por favor, insira o nome do produto.');
                nomeInput.focus();
                return;
            }
            
            if (!categoriaSelect.value) {
                e.preventDefault();
                alert('Por favor, selecione uma categoria.');
                categoriaSelect.focus();
                return;
            }
            
            if (!precoInput.value || parseFloat(precoInput.value) <= 0) {
                e.preventDefault();
                alert('Por favor, insira um preço válido.');
                precoInput.focus();
                return;
            }
            
            // Verificar se é necessário uma imagem para novos produtos
            <?php if (!$isEdit): ?>
            const imagemInput = document.getElementById('imagem');
            if (!imagemInput.files || imagemInput.files.length === 0) {
                e.preventDefault();
                alert('Por favor, selecione uma imagem para o produto.');
                return;
            }
            <?php endif; ?>
            
            // Loading no botão
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            submitBtn.disabled = true;
        });
        
        // Formatação do preço
        const precoInput = document.getElementById('preco');
        precoInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
    </script>
</body>
</html>

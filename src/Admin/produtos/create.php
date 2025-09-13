<?php
session_start();
require_once '../controllers/produto/Crud_produto.php';
require_once '../controllers/categoria/Crud_categoria.php';

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

try {
    $categorias = $crudCategoria->readAll();
} catch (Exception $e) {
    $categorias = [];
}

// Processar formulário
$message = '';
$message_type = '';

if ($_POST) {
    try {
        // Verificar se foi enviada uma imagem
        $imagem = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagem = $crudProduto->uploadImagem($_FILES['imagem']);
        }
        
        // Criar produto
        $resultado = $crudProduto->create(
            $_POST['nome_produto'],
            $_POST['descricao'],
            $_POST['preco'],
            $_POST['id_categoria'],
            $imagem ?: '',  // Se não há imagem, usar string vazia
            isset($_POST['ativo']) ? 1 : 0
        );
        
        if ($resultado) {
            $message = 'Produto criado com sucesso!';
            $message_type = 'success';
            // Limpar formulário
            $_POST = [];
        } else {
            $message = 'Erro ao criar produto.';
            $message_type = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Erro ao criar produto: ' . $e->getMessage();
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Produto - Ecoute Saveur Admin</title>
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
                    <h1><i class="fas fa-plus"></i> Novo Produto</h1>
                    <p>Adicionar novo produto ao cardápio</p>
                </div>
                <div class="page-actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar
                    </a>
                </div>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= $message ?>
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
                                        <div class="upload-placeholder" id="uploadPlaceholder">
                                            <i class="fas fa-camera"></i>
                                            <p>Clique para adicionar imagem</p>
                                        </div>
                                        <img id="imagePreview" class="image-preview" style="display: none;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage(this)" style="display: none;">
                                        <button type="button" class="btn btn-outline" onclick="document.getElementById('imagem').click()">
                                            <i class="fas fa-upload"></i>
                                            Escolher Imagem
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
                                           value="<?= htmlspecialchars($_POST['nome_produto'] ?? '') ?>" 
                                           placeholder="Ex: Pizza Margherita" required>
                                </div>

                                <div class="form-group">
                                    <label for="id_categoria">Categoria *</label>
                                    <select id="id_categoria" name="id_categoria" class="form-control" required>
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id_categoria'] ?>" 
                                                    <?= ($_POST['id_categoria'] ?? '') == $categoria['id_categoria'] ? 'selected' : '' ?>>
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
                                               value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>" 
                                               step="0.01" min="0" placeholder="0,00" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <textarea id="descricao" name="descricao" class="form-control" 
                                              rows="6" placeholder="Descreva o produto, ingredientes, etc..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <div class="checkbox-wrapper">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="ativo" <?= isset($_POST['ativo']) ? 'checked' : 'checked' ?>>
                                            <span class="checkmark"></span>
                                            Produto ativo (visível no cardápio)
                                        </label>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Criar Produto
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

    <script src="../js/admin.js"></script>
    <script>
        function previewImage(input) {
            const placeholder = document.getElementById('uploadPlaceholder');
            const preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
                placeholder.style.display = 'flex';
            }
        }

        // Validação do formulário
        document.querySelector('.product-form').addEventListener('submit', function(e) {
            const requiredFields = ['nome_produto', 'id_categoria', 'preco'];
            let hasError = false;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    input.style.borderColor = '#EF4444';
                    hasError = true;
                } else {
                    input.style.borderColor = '';
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        });

        // Preview da imagem ao clicar no placeholder
        document.getElementById('uploadPlaceholder').addEventListener('click', function() {
            document.getElementById('imagem').click();
        });
    </script>

    <style>
        .image-section {
            position: sticky;
            top: 20px;
        }
        
        .image-upload {
            margin-bottom: 20px;
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            background: var(--background-light);
        }
        
        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .upload-placeholder:hover {
            background: rgba(99, 102, 241, 0.05);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .upload-placeholder i {
            font-size: 48px;
            margin-bottom: 12px;
        }
        
        .image-preview {
            width: 100%;
            height: 250px;
            object-fit: cover;
            cursor: pointer;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-weight: 600;
            z-index: 1;
        }
        
        .input-group .form-control {
            padding-left: 35px;
        }
        
        .checkbox-wrapper {
            margin: 20px 0;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: 500;
        }
        
        .checkbox-label input[type="checkbox"] {
            display: none;
        }
        
        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-radius: 4px;
            margin-right: 12px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .checkbox-label input[type="checkbox"]:checked + .checkmark {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .checkbox-label input[type="checkbox"]:checked + .checkmark:after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        
        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .form-actions .btn {
            margin-right: 12px;
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
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }
    </style>
</body>
</html>

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

// Processar formulário
$message = '';
$message_type = '';

if ($_POST) {
    try {
        // Verificar se foi enviada uma nova imagem
        $imagem_atual = $produto['imagem'];
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagem_atual = $crudProduto->uploadImagem($_FILES['imagem'], $imagem_atual);
        }
        
        // Atualizar produto usando setters
        $crudProduto->setNome_produto($_POST['nome_produto']);
        $crudProduto->setDescricao($_POST['descricao']);
        $crudProduto->setPreco($_POST['preco']);
        $crudProduto->setId_categoria($_POST['id_categoria']);
        $crudProduto->setImagem($imagem_atual);
        $crudProduto->setEstoque((int)$_POST['estoque']); // Usar valor do formulário
        // Converter checkbox para enum do banco
        $status = isset($_POST['ativo']) ? 'Disponivel' : 'Indisponivel';
        $crudProduto->setStatus($status);
        
        $resultado = $crudProduto->update($id);
        
        if ($resultado) {
            $message = 'Produto atualizado com sucesso!';
            $message_type = 'success';
            // Recarregar dados atualizados
            $produto = $crudProduto->readById($id);
        } else {
            $message = 'Erro ao atualizar produto.';
            $message_type = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Erro ao atualizar produto: ' . $e->getMessage();
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - Ecoute Saveur Admin</title>
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
                    <h1><i class="fas fa-edit"></i> Editar Produto</h1>
                    <p>Modificar informações do produto</p>
                </div>
                <div class="page-actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar
                    </a>
                    <a href="view.php?id=<?= $produto['id_produto'] ?>" class="btn btn-info">
                        <i class="fas fa-eye"></i>
                        Visualizar
                    </a>
                </div>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= $message ?>
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
                    <h3><i class="fas fa-utensils"></i> Informações do Produto</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="product-form">
                        <div class="row">
                            <!-- Imagem Atual -->
                            <div class="col-md-4">
                                <div class="image-section">
                                    <label>Imagem Atual</label>
                                    <div class="current-image">
                                        <?php if ($produto['imagem']): ?>
                                            <img src="<?= getImageSrcAdmin($produto['imagem']) ?>" 
                                                 alt="<?= htmlspecialchars($produto['nome_produto']) ?>" 
                                                 id="currentImage">
                                        <?php else: ?>
                                            <div class="no-image">
                                                <i class="fas fa-image"></i>
                                                <p>Sem imagem</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="imagem">Nova Imagem</label>
                                        <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage(this)">
                                        <small class="form-text">Formatos aceitos: JPG, PNG, GIF (máx. 5MB)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulário -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nome_produto">Nome do Produto *</label>
                                    <input type="text" id="nome_produto" name="nome_produto" class="form-control" 
                                           value="<?= htmlspecialchars($produto['nome_produto']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="id_categoria">Categoria *</label>
                                    <select id="id_categoria" name="id_categoria" class="form-control" required>
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id_categoria'] ?>" 
                                                    <?= $produto['id_categoria'] == $categoria['id_categoria'] ? 'selected' : '' ?>>
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
                                               value="<?= number_format($produto['preco'], 2, '.', '') ?>" 
                                               step="0.01" min="0" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="estoque">Estoque *</label>
                                    <input type="number" id="estoque" name="estoque" class="form-control" 
                                           value="<?= htmlspecialchars($produto['estoque'] ?? 1) ?>" 
                                           min="0" required>
                                </div>

                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <textarea id="descricao" name="descricao" class="form-control" 
                                              rows="6" placeholder="Descreva o produto..."><?= htmlspecialchars($produto['descricao']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <div class="checkbox-wrapper">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="ativo" <?= ($produto['status'] == 'Disponivel') ? 'checked' : '' ?>>
                                            <span class="checkmark"></span>
                                            Produto ativo
                                        </label>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Salvar Alterações
                                    </button>
                                    <a href="view.php?id=<?= $produto['id_produto'] ?>" class="btn btn-secondary">
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
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const currentImage = document.getElementById('currentImage');
                    if (currentImage) {
                        currentImage.src = e.target.result;
                    } else {
                        // Se não há imagem atual, criar uma nova
                        const noImageDiv = document.querySelector('.no-image');
                        if (noImageDiv) {
                            noImageDiv.innerHTML = `<img src="${e.target.result}" alt="Preview" id="currentImage">`;
                        }
                    }
                }
                
                reader.readAsDataURL(input.files[0]);
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
    </script>

    <style>
        .current-image {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .current-image img {
            width: 100%;
            max-width: 250px;
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
            text-align: center;
        }
        
        .no-image i {
            font-size: 48px;
            margin-bottom: 12px;
        }
        
        .image-section {
            position: sticky;
            top: 20px;
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
    </style>
</body>
</html>

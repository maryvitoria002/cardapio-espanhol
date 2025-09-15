<?php
session_start();
require_once '../../controllers/categoria/Crud_categoria.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['message'] = 'ID da categoria não fornecido!';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

$crudCategoria = new Crud_categoria();
$categoria = $crudCategoria->readById($_GET['id']);

if (!$categoria) {
    $_SESSION['message'] = 'Categoria não encontrada!';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome_categoria = trim($_POST['nome_categoria']);
        $descricao = trim($_POST['descricao']);
        
        // Validações
        if (empty($nome_categoria)) {
            throw new Exception('Nome da categoria é obrigatório');
        }
        
        if (strlen($nome_categoria) < 2) {
            throw new Exception('Nome da categoria deve ter pelo menos 2 caracteres');
        }
        
        if (strlen($nome_categoria) > 100) {
            throw new Exception('Nome da categoria deve ter no máximo 100 caracteres');
        }
        
        // Verificar se já existe (excluindo a atual)
        if ($crudCategoria->nomeExists($nome_categoria, $_GET['id'])) {
            throw new Exception('Já existe uma categoria com este nome');
        }
        
        // Atualizar categoria usando setters
        $crudCategoria->setNomeCategoria($nome_categoria);
        $crudCategoria->setDescricao($descricao);
        
        $success = $crudCategoria->update($_GET['id']);
        
        if ($success) {
            $_SESSION['message'] = 'Categoria atualizada com sucesso!';
            $_SESSION['message_type'] = 'success';
            header('Location: view.php?id=' . $_GET['id']);
            exit;
        } else {
            throw new Exception('Erro ao atualizar categoria');
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
    <title>Editar <?= htmlspecialchars($categoria['nome_categoria']) ?> - Ecoute Saveur Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .category-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-section h3 {
            margin: 0 0 20px 0;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group .required {
            color: #dc3545;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        .form-control.error {
            border-color: #dc3545;
        }

        .form-help {
            font-size: 13px;
            color: #6c757d;
            margin-top: 5px;
        }

        .char-counter {
            font-size: 12px;
            color: #6c757d;
            text-align: right;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
        }

        .preview-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .preview-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .preview-description {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.5;
        }

        .current-info {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .current-info h4 {
            margin: 0 0 10px 0;
            color: #0c5460;
            font-size: 14px;
            font-weight: 600;
        }

        .current-info p {
            margin: 0;
            color: #0c5460;
        }

        @media (max-width: 768px) {
            .category-form {
                max-width: 100%;
            }
            
            .form-section {
                padding: 20px;
            }
            
            .form-actions {
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
                    <h1><i class="fas fa-edit"></i> Editar Categoria</h1>
                    <p>Modificar informações da categoria</p>
                </div>
                <div class="page-actions">
                    <a href="view.php?id=<?= $categoria['id_categoria'] ?>" class="btn btn-info">
                        <i class="fas fa-eye"></i>
                        Visualizar
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar
                    </a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="category-form">
                <form method="POST" id="categoryForm">
                    <div class="form-section">
                        <h3><i class="fas fa-info-circle"></i> Informações Atuais</h3>
                        
                        <div class="current-info">
                            <h4>Nome Atual</h4>
                            <p><?= htmlspecialchars($categoria['nome_categoria']) ?></p>
                            
                            <?php if (!empty($categoria['descricao'])): ?>
                                <h4 style="margin-top: 15px;">Descrição Atual</h4>
                                <p><?= htmlspecialchars($categoria['descricao']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-edit"></i> Editar Informações</h3>
                        
                        <div class="form-group">
                            <label for="nome_categoria">
                                Nome da Categoria <span class="required">*</span>
                            </label>
                            <input type="text" id="nome_categoria" name="nome_categoria" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['nome_categoria'] ?? $categoria['nome_categoria']) ?>" 
                                   placeholder="Ex: Bebidas, Pratos Principais, Sobremesas..." 
                                   maxlength="100" required>
                            <div class="char-counter">
                                <span id="nameCounter">0</span>/100 caracteres
                            </div>
                            <div class="form-help">
                                Escolha um nome claro e descritivo para a categoria
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição (Opcional)</label>
                            <textarea id="descricao" name="descricao" class="form-control" 
                                      rows="4" maxlength="500"
                                      placeholder="Descreva brevemente esta categoria..."><?= htmlspecialchars($_POST['descricao'] ?? $categoria['descricao']) ?></textarea>
                            <div class="char-counter">
                                <span id="descCounter">0</span>/500 caracteres
                            </div>
                            <div class="form-help">
                                Uma descrição opcional que aparecerá no cardápio
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-eye"></i> Prévia das Alterações</h3>
                        <div class="preview-section">
                            <div class="preview-title" id="previewTitle"><?= htmlspecialchars($categoria['nome_categoria']) ?></div>
                            <div class="preview-description" id="previewDescription"><?= htmlspecialchars($categoria['descricao']) ?: 'Descrição da categoria aparecerá aqui...' ?></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Salvar Alterações
                        </button>
                        <a href="view.php?id=<?= $categoria['id_categoria'] ?>" class="btn btn-info">
                            <i class="fas fa-eye"></i>
                            Visualizar
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const nomeInput = document.getElementById('nome_categoria');
        const descInput = document.getElementById('descricao');
        const nameCounter = document.getElementById('nameCounter');
        const descCounter = document.getElementById('descCounter');
        const previewTitle = document.getElementById('previewTitle');
        const previewDescription = document.getElementById('previewDescription');
        const form = document.getElementById('categoryForm');

        // Contador de caracteres e preview em tempo real
        function updateCounters() {
            const nomeLength = nomeInput.value.length;
            const descLength = descInput.value.length;
            
            nameCounter.textContent = nomeLength;
            descCounter.textContent = descLength;
            
            // Atualizar preview
            previewTitle.textContent = nomeInput.value || '<?= htmlspecialchars($categoria['nome_categoria']) ?>';
            previewDescription.textContent = descInput.value || 'Descrição da categoria aparecerá aqui...';
            
            // Validação visual
            nameCounter.style.color = nomeLength > 90 ? '#dc3545' : '#6c757d';
            descCounter.style.color = descLength > 450 ? '#dc3545' : '#6c757d';
        }

        nomeInput.addEventListener('input', updateCounters);
        descInput.addEventListener('input', updateCounters);

        // Inicializar contadores
        updateCounters();

        // Validação do formulário
        form.addEventListener('submit', function(e) {
            const nome = nomeInput.value.trim();
            
            if (!nome) {
                e.preventDefault();
                alert('Por favor, insira o nome da categoria.');
                nomeInput.focus();
                return;
            }
            
            if (nome.length < 2) {
                e.preventDefault();
                alert('O nome da categoria deve ter pelo menos 2 caracteres.');
                nomeInput.focus();
                return;
            }
            
            // Loading no botão
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            submitBtn.disabled = true;
        });

        // Capitalizar primeira letra
        nomeInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                updateCounters();
            }
        });
    });
    </script>
</body>
</html>

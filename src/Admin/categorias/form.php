<?php
session_start();
require_once '../../db/conection.php';
require_once '../../controllers/categoria/Crud_categoria.php';

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Criar sessão admin automática
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

$message = '';
$message_type = '';
$categoria = null;
$isEdit = false;

// Verificar se é edição
if (isset($_GET['id'])) {
    $isEdit = true;
    $crud = new Crud_categoria();
    try {
        $categoria = $crud->readById($_GET['id']);
        if (!$categoria) {
            header('Location: index.php?error=Categoria não encontrada');
            exit();
        }
    } catch (Exception $e) {
        header('Location: index.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}

// Processar formulário
if ($_POST) {
    try {
        $crud = new Crud_categoria();
        
        // Validações
        $nome_categoria = trim($_POST['nome_categoria'] ?? '');
        
        if (empty($nome_categoria)) {
            throw new Exception('Nome da categoria é obrigatório.');
        }
        
        if (strlen($nome_categoria) > 20) {
            throw new Exception('Nome da categoria deve ter no máximo 20 caracteres.');
        }
        
        // Setar valores no objeto
        $crud->setNomeCategoria($nome_categoria);
        
        if ($isEdit) {
            $result = $crud->update($_GET['id']);
            $message = 'Categoria atualizada com sucesso!';
        } else {
            $result = $crud->create();
            $message = 'Categoria criada com sucesso!';
        }
        
        $message_type = 'success';
        
        // Redirecionar após sucesso
        header('Location: index.php?success=' . urlencode($message));
        exit();
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Editar' : 'Nova' ?> Categoria - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .char-counter {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>
                <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i>
                <?= $isEdit ? 'Editar' : 'Nova' ?> Categoria
            </h1>
        </div>
        
        <div class="form-container">
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nome_categoria">Nome da Categoria *</label>
                    <input type="text" id="nome_categoria" name="nome_categoria" 
                           value="<?= $categoria ? htmlspecialchars($categoria['nome_categoria']) : '' ?>" 
                           maxlength="20" required>
                    <div class="char-counter">
                        <span id="char-count">0</span>/20 caracteres
                    </div>
                </div>
                
                <div class="btn-group">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Atualizar' : 'Criar' ?> Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Contador de caracteres
        const input = document.getElementById('nome_categoria');
        const counter = document.getElementById('char-count');
        
        function updateCounter() {
            counter.textContent = input.value.length;
            if (input.value.length > 15) {
                counter.style.color = '#dc3545';
            } else {
                counter.style.color = '#666';
            }
        }
        
        input.addEventListener('input', updateCounter);
        updateCounter(); // Inicializar
    </script>
</body>
</html>

<?php
session_start();
require_once '../../db/conection.php';
require_once '../../controllers/usuario/Crud_usuario.php';

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Criar sessão admin automática
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

$message = '';
$message_type = '';
$usuario = null;

// Verificar se é edição
if (!isset($_GET['id'])) {
    header('Location: index.php?error=ID do usuário não fornecido');
    exit();
}

$crud = new Crud_usuario();
try {
    $usuario = $crud->readById($_GET['id']);
    if (!$usuario) {
        header('Location: index.php?error=Usuário não encontrado');
        exit();
    }
} catch (Exception $e) {
    header('Location: index.php?error=' . urlencode($e->getMessage()));
    exit();
}

// Processar formulário
if ($_POST) {
    try {
        // Validações
        $primeiro_nome = trim($_POST['primeiro_nome'] ?? '');
        $segundo_nome = trim($_POST['segundo_nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';
        
        if (empty($primeiro_nome)) {
            throw new Exception('Primeiro nome é obrigatório.');
        }
        
        if (empty($segundo_nome)) {
            throw new Exception('Segundo nome é obrigatório.');
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('E-mail válido é obrigatório.');
        }
        
        // Verificar se email já existe
        if ($crud->emailExists($email, $_GET['id'])) {
            throw new Exception('Este e-mail já está sendo usado por outro usuário.');
        }
        
        if (!empty($senha)) {
            if ($senha !== $confirmar_senha) {
                throw new Exception('Senhas não conferem.');
            }
            if (strlen($senha) < 6) {
                throw new Exception('Senha deve ter pelo menos 6 caracteres.');
            }
        }
        
        // Setar valores no objeto
        $crud->setPrimeiroNome($primeiro_nome);
        $crud->setSegundoNome($segundo_nome);
        $crud->setEmail($email);
        $crud->setTelefone($telefone);
        $crud->setEndereco($endereco);
        
        if (!empty($senha)) {
            $crud->setSenha($senha);
        }
        
        // Processar imagem
        if (isset($_FILES['imagem_perfil']) && $_FILES['imagem_perfil']['error'] === UPLOAD_ERR_OK) {
            $imagem = $crud->uploadImagem($_FILES['imagem_perfil']);
            $crud->setImagemUsuario($imagem);
        } else {
            $crud->setImagemUsuario($usuario['imagem_perfil']);
        }
        
        $result = $crud->update($_GET['id']);
        $message = 'Usuário atualizado com sucesso!';
        $message_type = 'success';
        
        // Recarregar dados do usuário
        $usuario = $crud->readById($_GET['id']);
        
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
    <title>Editar Usuário - Ecoute Saveur Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
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
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group textarea {
            height: 80px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
            color: white;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
            color: white;
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
        
        .image-preview {
            margin-top: 10px;
            max-width: 100px;
        }
        
        .image-preview img {
            width: 100%;
            height: auto;
            border-radius: 50%;
            border: 2px solid #ddd;
        }
        
        .password-note {
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
                <i class="fas fa-edit"></i>
                Editar Usuário
            </h1>
        </div>
        
        <div class="form-container">
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="primeiro_nome">Primeiro Nome *</label>
                        <input type="text" id="primeiro_nome" name="primeiro_nome" 
                               value="<?= htmlspecialchars($usuario['primeiro_nome']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="segundo_nome">Segundo Nome *</label>
                        <input type="text" id="segundo_nome" name="segundo_nome" 
                               value="<?= htmlspecialchars($usuario['segundo_nome']) ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input type="email" id="email" name="email" 
                               value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" 
                               value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <textarea id="endereco" name="endereco" placeholder="Endereço completo..."><?= htmlspecialchars($usuario['endereco'] ?? '') ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="senha">Nova Senha</label>
                        <input type="password" id="senha" name="senha">
                        <div class="password-note">Deixe em branco para manter a senha atual</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Nova Senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="imagem_perfil">Imagem de Perfil</label>
                    <input type="file" id="imagem_perfil" name="imagem_perfil" accept="image/*">
                    <?php if ($usuario['imagem_perfil']): ?>
                        <div class="image-preview">
                            <img src="../../images/usuarios/<?= htmlspecialchars($usuario['imagem_perfil']) ?>" 
                                 alt="Imagem atual" onerror="this.src='../../assets/avatar.png'">>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="btn-group">
                    <a href="view.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-info">
                        <i class="fas fa-eye"></i> Visualizar
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Atualizar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Validação de senhas
        const senha = document.getElementById('senha');
        const confirmarSenha = document.getElementById('confirmar_senha');
        
        function validarSenhas() {
            if (senha.value !== confirmarSenha.value) {
                confirmarSenha.setCustomValidity('As senhas não conferem');
            } else {
                confirmarSenha.setCustomValidity('');
            }
        }
        
        senha.addEventListener('input', validarSenhas);
        confirmarSenha.addEventListener('input', validarSenhas);
        
        // Preview da imagem
        document.getElementById('imagem_perfil').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'image-preview';
                        e.target.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>

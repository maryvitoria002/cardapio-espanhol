<?php
session_start();
require_once '../../db/conection.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Criar sessão admin automática
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

$message = '';
$message_type = '';

if ($_POST) {
    try {
        $primeiro_nome = $_POST['primeiro_nome'] ?? '';
        $segundo_nome = $_POST['segundo_nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $imagem_perfil = 'default.jpg';
        
        // Validações básicas
        if (empty($primeiro_nome) || empty($segundo_nome) || empty($email) || empty($senha)) {
            throw new Exception('Todos os campos obrigatórios devem ser preenchidos.');
        }
        
        // Verificar se email já existe
        $pdo = new Database();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Este email já está em uso.');
        }
        
        // Upload da imagem
        if (!empty($_FILES['imagem']['tmp_name'])) {
            $upload_dir = '../../images/';
            $file_extension = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $imagem_perfil = 'user_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $imagem_perfil;
                
                if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_path)) {
                    throw new Exception('Erro ao fazer upload da imagem.');
                }
            } else {
                throw new Exception('Formato de imagem não permitido. Use JPG, PNG ou GIF.');
            }
        }
        
        // Inserir usuário
        $stmt = $pdo->prepare("
            INSERT INTO usuario (primeiro_nome, segundo_nome, email, telefone, senha, imagem_perfil) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $primeiro_nome,
            $segundo_nome,
            $email,
            $telefone,
            $senha, // Senha sem criptografia
            $imagem_perfil
        ]);
        
        header('Location: index.php?message=Usuário criado com sucesso!&type=success');
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
    <title>Novo Usuário - RestauranteSIS Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <link href="css/crud.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1><i class="fas fa-user-plus"></i> Novo Usuário</h1>
                <p>Cadastrar novo usuário no sistema</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <div class="form-header">
                    <h2><i class="fas fa-user"></i> Dados do Usuário</h2>
                </div>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-content">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="primeiro_nome">Primeiro Nome *</label>
                                <input type="text" id="primeiro_nome" name="primeiro_nome" required 
                                       value="<?= htmlspecialchars($_POST['primeiro_nome'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="segundo_nome">Segundo Nome *</label>
                                <input type="text" id="segundo_nome" name="segundo_nome" required 
                                       value="<?= htmlspecialchars($_POST['segundo_nome'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" 
                                       value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="senha">Senha *</label>
                                <input type="password" id="senha" name="senha" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="imagem">Foto do Perfil</label>
                                <div class="image-upload">
                                    <img src="../../assets/avatar.png" alt="Preview" class="image-preview" id="imagePreview">
                                    <div class="upload-btn btn btn-outline">
                                        <i class="fas fa-camera"></i>
                                        Escolher Foto
                                        <input type="file" id="imagem" name="imagem" accept="image/*" 
                                               onchange="previewImage(this, 'imagePreview')">
                                    </div>
                                </div>
                                <div class="help-text">Formatos aceitos: JPG, PNG, GIF (max 2MB)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Salvar Usuário
                        </button>
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
</body>
</html>

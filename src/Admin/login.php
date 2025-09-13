<?php
session_start();

// Verificar se já está logado
if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Tentar incluir arquivo de conexão
try {
    require_once '../db/conection.php';
    
    // Criar instância da conexão para testar
    $database = new Database();
    $conexao = $database->getInstance();
    
    // Verificar se a conexão foi estabelecida
    if (!isset($conexao)) {
        throw new Exception('Erro ao conectar com o banco de dados.');
    }
} catch (Exception $e) {
    $error = 'Erro de conexão: ' . $e->getMessage();
}

if ($_POST && !$error) {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        try {
            // Criar instância da conexão
            $database = new Database();
            $conexao = $database->getInstance();
            
            $stmt = $conexao->prepare("SELECT * FROM funcionario WHERE email = ?");
            $stmt->execute([$email]);
            $funcionario = $stmt->fetch();
            
            if ($funcionario && password_verify($senha, $funcionario['senha'])) {
                // Login bem-sucedido
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_id'] = $funcionario['id_funcionario'];
                $_SESSION['admin_nome'] = $funcionario['nome'] . ' ' . $funcionario['sobrenome'];
                $_SESSION['admin_acesso'] = $funcionario['nivel_acesso'] ?? 'admin';
                $_SESSION['admin_email'] = $funcionario['email'];
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Email ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $error = 'Erro no sistema: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestauranteSIS - Admin Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-utensils"></i>
                </div>
                <h1>RestauranteSIS</h1>
                <p>Painel Administrativo</p>
            </div>
            
            <form method="POST" class="login-form">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Credenciais de teste -->
                <div class="test-credentials">
                    <small><strong>Credenciais de teste:</strong></small><br>
                    <small>Email: maryleloli1811@gmail.com</small><br>
                    <small>Senha: 123</small>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        placeholder="Digite seu email"
                    >
                </div>
                
                <div class="form-group">
                    <label for="senha">
                        <i class="fas fa-lock"></i>
                        Senha
                    </label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        required 
                        placeholder="Digite sua senha"
                    >
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Entrar
                </button>
            </form>
            
            <div class="login-footer">
                <p>&copy; 2025 RestauranteSIS. Todos os direitos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>

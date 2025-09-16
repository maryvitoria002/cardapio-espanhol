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
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        try {
            // Criar instância da conexão
            $database = new Database();
            $conexao = $database->getInstance();
            
            // Preparar consulta com mais campos
            $stmt = $conexao->prepare("SELECT id_funcionario, nome, email, senha, cargo, telefone, data_criacao FROM funcionario WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($funcionario && password_verify($senha, $funcionario['senha'])) {
                // Login bem-sucedido - registrar dados da sessão
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_id'] = $funcionario['id_funcionario'];
                $_SESSION['admin_nome'] = $funcionario['nome'];
                $_SESSION['admin_email'] = $funcionario['email'];
                $_SESSION['admin_cargo'] = $funcionario['cargo'];
                $_SESSION['admin_telefone'] = $funcionario['telefone'];
                $_SESSION['login_time'] = time();
                
                // Registrar último login (opcional - se houver campo na tabela)
                try {
                    $updateStmt = $conexao->prepare("UPDATE funcionario SET ultimo_login = NOW() WHERE id_funcionario = ?");
                    $updateStmt->execute([$funcionario['id_funcionario']]);
                } catch (Exception $e) {
                    // Ignorar se não houver campo ultimo_login
                }
                
                $success = 'Login realizado com sucesso! Redirecionando...';
                header('Refresh: 2; URL=index.php');
            } else {
                $error = 'Email ou senha incorretos. Verifique suas credenciais.';
            }
        } catch (PDOException $e) {
            $error = 'Erro no sistema: Não foi possível processar o login.';
            error_log('Erro de login admin: ' . $e->getMessage());
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
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1>Écoute Saveur</h1>
                <p>Painel Administrativo</p>
            </div>
            
            <form method="POST" class="login-form" id="adminLoginForm">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Credenciais de teste -->
                <div class="test-credentials">
                    <small><strong>Credenciais de teste:</strong></small><br>
                    <small><i class="fas fa-envelope"></i> Email: admin@restaurante.com</small><br>
                    <small><i class="fas fa-key"></i> Senha: admin123</small>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Administrativo
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        placeholder="Digite seu email administrativo"
                        autocomplete="email"
                    >
                </div>
                
                <div class="form-group">
                    <label for="senha">
                        <i class="fas fa-lock"></i>
                        Senha
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            required 
                            placeholder="Digite sua senha"
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Acessar Painel</span>
                </button>
                
                <div class="form-footer">
                    <small><i class="fas fa-info-circle"></i> Acesso restrito apenas para funcionários autorizados</small>
                </div>
            </form>
            
            <div class="login-footer">
                <p>&copy; 2025 Écoute Saveur. Todos os direitos reservados.</p>
                <small>Sistema de gestão administrativo v2.0</small>
            </div>
        </div>
    </div>

    <script>
        // Função para mostrar/ocultar senha
        function togglePassword() {
            const senhaInput = document.getElementById('senha');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                senhaInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Validação do formulário e feedback visual
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const btnText = loginBtn.querySelector('span');
            const btnIcon = loginBtn.querySelector('i');
            
            // Desabilitar botão e mostrar loading
            loginBtn.disabled = true;
            btnIcon.className = 'fas fa-spinner fa-spin';
            btnText.textContent = 'Verificando...';
            
            // Se houver erro na validação, reabilitar o botão
            setTimeout(() => {
                if (!e.defaultPrevented) {
                    // Formulário será enviado
                }
            }, 100);
        });

        // Auto-focus no campo email
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });

        // Mensagens de sucesso/erro com auto-hide
        <?php if ($success): ?>
        setTimeout(() => {
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 300);
            }
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>

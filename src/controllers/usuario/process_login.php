<?php
header('Content-Type: application/json');
session_start();

// Incluir conexão com banco
require_once __DIR__ . '/../../db/conection.php';

try {
    // Criar instância da conexão
    $database = new Database();
    $pdo = $database->getInstance();
    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    // Receber dados do formulário
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $lembrar = isset($_POST['lembrar']) ? true : false;

    // Validações básicas
    if (empty($email) || empty($senha)) {
        throw new Exception('Email e senha são obrigatórios');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Buscar usuário no banco
    $stmt = $pdo->prepare("SELECT id, nome, sobrenome, email, senha FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception('Email ou senha incorretos');
    }

    // Verificar senha
    $senha_valida = false;
    
    // Primeiro tentar com password_hash (novo sistema)
    if (password_verify($senha, $usuario['senha'])) {
        $senha_valida = true;
    } 
    // Se não funcionar, tentar com MD5 (sistema antigo) e migrar
    else if (md5($senha) === $usuario['senha']) {
        $senha_valida = true;
        
        // Migrar para password_hash
        $nova_senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt_update = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt_update->execute([$nova_senha_hash, $usuario['id']]);
    }

    if (!$senha_valida) {
        throw new Exception('Email ou senha incorretos');
    }

    // Criar sessão
    $_SESSION['id'] = $usuario['id'];
    $_SESSION['primeiro_nome'] = $usuario['nome'];
    $_SESSION['segundo_nome'] = $usuario['sobrenome'];
    $_SESSION['email'] = $usuario['email'];

    // Configurar cookie se "lembrar" estiver marcado
    if ($lembrar) {
        $cookie_value = base64_encode(json_encode([
            'id' => $usuario['id'],
            'email' => $usuario['email'],
            'hash' => password_hash($usuario['email'] . $usuario['id'], PASSWORD_DEFAULT)
        ]));
        setcookie('lembrar_login', $cookie_value, time() + (30 * 24 * 60 * 60), '/'); // 30 dias
    }

    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Login realizado com sucesso!',
        'redirect' => './index.php'
    ]);

} catch (Exception $e) {
    // Log do erro (opcional)
    error_log("Erro no login: " . $e->getMessage());
    
    // Resposta de erro
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

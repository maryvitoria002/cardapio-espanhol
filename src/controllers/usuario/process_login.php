<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Incluir conexão com banco
require_once __DIR__ . '/../../db/conection.php';

try {
    // Criar instância da conexão
    $database = new Database();
    $pdo = $database->getInstance();
    
    if (!$pdo) {
        throw new Exception('Falha ao conectar com o banco de dados');
    }
    
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
    $stmt = $pdo->prepare("SELECT id_usuario, primeiro_nome, segundo_nome, email, senha, imagem_perfil FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception('Email ou senha incorretos');
    }

    // Verificar senha
    // Verificar senha sem criptografia
    if ($senha !== $usuario['senha']) {
        throw new Exception('Email ou senha incorretos');
    }

    // Criar sessão
    $_SESSION['id'] = $usuario['id_usuario'];
    $_SESSION['primeiro_nome'] = $usuario['primeiro_nome'];
    $_SESSION['segundo_nome'] = $usuario['segundo_nome'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['foto_perfil'] = $usuario['imagem_perfil'];

    // Configurar cookie se "lembrar" estiver marcado
    if ($lembrar) {
        $cookie_value = base64_encode(json_encode([
            'id' => $usuario['id_usuario'],
            'email' => $usuario['email']
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

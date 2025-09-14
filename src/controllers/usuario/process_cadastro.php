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
    $nome = trim($_POST['primeiro_nome'] ?? '');
    $sobrenome = trim($_POST['segundo_nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $endereco = trim($_POST['endereco'] ?? '');
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    $aceitar_termos = isset($_POST['aceitar_termos']) ? 1 : 0;

    // Validações básicas
    if (empty($nome) || empty($sobrenome) || empty($email) || empty($telefone) || empty($senha)) {
        throw new Exception('Todos os campos são obrigatórios');
    }

    if (!$aceitar_termos) {
        throw new Exception('Você deve aceitar os termos de serviço');
    }

    if ($senha !== $confirmar_senha) {
        throw new Exception('As senhas não coincidem');
    }

    if (strlen($senha) < 6) {
        throw new Exception('A senha deve ter pelo menos 6 caracteres');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Verificar se email já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        throw new Exception('Este email já está cadastrado');
    }

    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir usuário no banco
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nome, sobrenome, email, telefone, senha, endereco, data_nascimento, data_cadastro) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $success = $stmt->execute([$nome, $sobrenome, $email, $telefone, $senha_hash, $endereco, $data_nascimento ?: null]);

    if (!$success) {
        throw new Exception('Erro ao cadastrar usuário');
    }

    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Cadastro realizado com sucesso!',
        'redirect' => './login.php?message=' . urlencode('Cadastro realizado! Faça seu login.') . '&type=success'
    ]);

} catch (Exception $e) {
    // Log do erro (opcional)
    error_log("Erro no cadastro: " . $e->getMessage());
    
    // Resposta de erro
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

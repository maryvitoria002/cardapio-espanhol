<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

require_once __DIR__ . '/../../controllers/funcionario/Crud_funcionario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID do funcionário é obrigatório');
    }
    
    $crudFuncionario = new Crud_funcionario();
    $funcionario = $crudFuncionario->readById($_GET['id']);
    
    if (!$funcionario) {
        throw new Exception('Funcionário não encontrado');
    }
    
    echo json_encode([
        'success' => true, 
        'data' => $funcionario
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

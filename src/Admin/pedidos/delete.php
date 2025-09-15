<?php
session_start();
require_once __DIR__ . '/../../db/conection.php';
require_once __DIR__ . '/../../models/Crud_pedido.php';

// Criar instância da conexão
$database = new Database();
$conexao = $database->getInstance();

// Verificar se está logado como admin
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obter dados JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verificar se foi passado o ID
if (!isset($input['id']) || empty($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do pedido não informado']);
    exit;
}

$idPedido = (int)$input['id'];

try {
    $crudPedido = new Crud_pedido($conexao);
    
    // Verificar se o pedido existe
    $pedido = $crudPedido->readById($idPedido);
    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
        exit;
    }
    
    // Excluir pedido
    $resultado = $crudPedido->deleteById($idPedido);
    
    if ($resultado) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pedido excluído com sucesso!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir pedido']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>

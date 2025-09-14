<?php
session_start();
require_once __DIR__ . '/../../db/conection.php';
require_once __DIR__ . '/../../controllers/pedido/Crud_pedido.php';

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

// Verificar dados obrigatórios
if (!isset($_POST['id_pedido']) || !isset($_POST['status']) || 
    empty($_POST['id_pedido']) || empty($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

$idPedido = (int)$_POST['id_pedido'];
$novoStatus = trim($_POST['status']);
$observacoes = trim($_POST['observacoes'] ?? '');

// Validar status
$statusValidos = ['Pendente', 'Concluido', 'Cancelado', 'Processando', 'A caminho'];
if (!in_array($novoStatus, $statusValidos)) {
    echo json_encode(['success' => false, 'message' => 'Status inválido']);
    exit;
}

try {
    $crudPedido = new Crud_pedido($conexao);
    
    // Verificar se o pedido existe
    $pedido = $crudPedido->readById($idPedido);
    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
        exit;
    }
    
    // Atualizar status
    $resultado = $crudPedido->updateStatus($idPedido, $novoStatus, $observacoes);
    
    if ($resultado) {
        echo json_encode([
            'success' => true, 
            'message' => 'Status do pedido atualizado com sucesso!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status do pedido']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>

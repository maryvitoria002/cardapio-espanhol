<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit();
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

// Verificar se o ID do pedido foi fornecido
if (!isset($_POST['id_pedido']) || empty($_POST['id_pedido'])) {
    echo json_encode(['success' => false, 'message' => 'ID do pedido não fornecido']);
    exit();
}

$id_pedido = (int) $_POST['id_pedido'];
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : 'Cancelado pelo usuário';

try {
    require_once "./controllers/pedido/Crud_pedido.php";
    
    $crudPedidos = new Crud_pedido();
    
    // Verificar se o pedido pertence ao usuário logado
    $pedido = $crudPedidos->readById($id_pedido);
    
    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
        exit();
    }
    
    if ($pedido['id_usuario'] != $_SESSION['id']) {
        echo json_encode(['success' => false, 'message' => 'Você não tem permissão para cancelar este pedido']);
        exit();
    }
    
    // Verificar se o pedido ainda pode ser cancelado (apenas pendentes)
    if (strtolower($pedido['status_pedido']) !== 'pendente') {
        echo json_encode(['success' => false, 'message' => 'Este pedido não pode mais ser cancelado']);
        exit();
    }
    
    // Cancelar o pedido
    $database = new Database();
    $conexao = $database->getInstance();
    
    $sql = "UPDATE pedido SET status_pedido = 'Cancelado', motivo_cancelamento = :motivo WHERE id_pedido = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':motivo', $motivo, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id_pedido, PDO::PARAM_INT);
    
    $resultado = $stmt->execute();
    
    if ($resultado) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pedido cancelado com sucesso!'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao cancelar o pedido. Tente novamente.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}
?>

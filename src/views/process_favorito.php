<?php
header('Content-Type: application/json');
session_start();

// Verificar se o usuário está logado
if (empty($_SESSION["id"])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

// Incluir controlador de favoritos
require_once '../models/Crud_favorito.php';

try {
    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    $action = $_POST['action'] ?? '';
    $id_produto = intval($_POST['id_produto'] ?? 0);

    if (empty($action) || $id_produto <= 0) {
        throw new Exception('Parâmetros inválidos');
    }

    $crudFavorito = new Crud_favorito();
    $id_usuario = $_SESSION["id"];

    switch ($action) {
        case 'toggle':
            $result = $crudFavorito->toggleFavorito($id_usuario, $id_produto);
            echo json_encode([
                'success' => true,
                'action' => $result['action'],
                'message' => $result['message']
            ]);
            break;

        case 'add':
            $success = $crudFavorito->adicionarFavorito($id_usuario, $id_produto);
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'action' => 'added',
                    'message' => 'Produto adicionado aos favoritos'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Produto já está nos favoritos'
                ]);
            }
            break;

        case 'remove':
            $success = $crudFavorito->removerFavorito($id_usuario, $id_produto);
            echo json_encode([
                'success' => $success,
                'action' => 'removed',
                'message' => $success ? 'Produto removido dos favoritos' : 'Erro ao remover favorito'
            ]);
            break;

        case 'check':
            $isFavorito = $crudFavorito->isFavorito($id_usuario, $id_produto);
            echo json_encode([
                'success' => true,
                'is_favorite' => $isFavorito
            ]);
            break;

        default:
            throw new Exception('Ação não reconhecida');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
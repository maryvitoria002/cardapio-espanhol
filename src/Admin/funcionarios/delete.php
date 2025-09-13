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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || empty($input['id'])) {
        throw new Exception('ID do funcionário é obrigatório');
    }
    
    $crudFuncionario = new Crud_funcionario();
    
    // Verificar se o funcionário existe
    $funcionario = $crudFuncionario->readById($input['id']);
    if (!$funcionario) {
        throw new Exception('Funcionário não encontrado');
    }
    
    // Excluir funcionário
    if ($crudFuncionario->deleteById($input['id'])) {
        // Remover imagem se existir
        if (!empty($funcionario['imagem_perfil'])) {
            $caminho_imagem = '../uploads/funcionarios/' . $funcionario['imagem_perfil'];
            if (file_exists($caminho_imagem)) {
                unlink($caminho_imagem);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Funcionário excluído com sucesso']);
    } else {
        throw new Exception('Erro ao excluir funcionário');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

<?php
require_once __DIR__ . '/../../../controllers/usuario/Crud_usuario.php';

session_start();

// Verificar se o usuário está logado como admin
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

// Definir o tipo de conteúdo como JSON
header('Content-Type: application/json');

try {
    $crudUsuario = new Crud_usuario();
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['action'])) {
                $action = $_GET['action'];
                
                switch ($action) {
                    case 'list':
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $status = isset($_GET['status']) ? $_GET['status'] : '';
                        $limit = 10;
                        $offset = ($page - 1) * $limit;
                        
                        $usuarios = $crudUsuario->readAll($search, $status, $offset, $limit);
                        $total = $crudUsuario->count($search, $status);
                        $totalPages = ceil($total / $limit);
                        
                        echo json_encode([
                            'success' => true,
                            'data' => $usuarios,
                            'pagination' => [
                                'current_page' => $page,
                                'total_pages' => $totalPages,
                                'total_records' => $total,
                                'per_page' => $limit
                            ]
                        ]);
                        break;
                        
                    case 'get':
                        if (isset($_GET['id'])) {
                            $usuario = $crudUsuario->readById($_GET['id']);
                            if ($usuario) {
                                echo json_encode(['success' => true, 'data' => $usuario]);
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
                        }
                        break;
                        
                    case 'stats':
                        $total = $crudUsuario->count();
                        echo json_encode([
                            'success' => true,
                            'data' => [
                                'total' => $total
                            ]
                        ]);
                        break;
                        
                    default:
                        echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
                break;
            }
            
            // Validações básicas
            if (empty($data['primeiro_nome']) || empty($data['segundo_nome']) || empty($data['email'])) {
                echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
                break;
            }
            
            // Hash da senha se fornecida
            if (!empty($data['senha'])) {
                $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
            } else {
                echo json_encode(['success' => false, 'message' => 'Senha é obrigatória']);
                break;
            }
            
            if ($crudUsuario->createUser($data)) {
                echo json_encode(['success' => true, 'message' => 'Usuário criado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao criar usuário']);
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
                break;
            }
            
            $id = $data['id'];
            unset($data['id']);
            
            // Validações básicas
            if (empty($data['primeiro_nome']) || empty($data['segundo_nome']) || empty($data['email'])) {
                echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
                break;
            }
            
            // Hash da senha se fornecida
            if (!empty($data['senha'])) {
                $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
            } else {
                // Remove senha dos dados se não fornecida
                unset($data['senha']);
            }
            
            if ($crudUsuario->updateUser($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar usuário']);
            }
            break;
            
        case 'DELETE':
            if (isset($_GET['id'])) {
                if ($crudUsuario->delete($_GET['id'])) {
                    echo json_encode(['success' => true, 'message' => 'Usuário excluído com sucesso']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao excluir usuário']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

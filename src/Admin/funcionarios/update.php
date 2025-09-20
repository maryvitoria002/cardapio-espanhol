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
    $crudFuncionario = new Crud_funcionario();
    
    // Validar campos obrigatórios
    if (empty($_POST['id_funcionario']) || empty($_POST['primeiro_nome']) || 
        empty($_POST['segundo_nome']) || empty($_POST['email']) || 
        empty($_POST['telefone']) || empty($_POST['acesso'])) {
        throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
    }
    
    $id = $_POST['id_funcionario'];
    
    // Verificar se o email já existe (excluindo o próprio funcionário)
    if ($crudFuncionario->emailExists($_POST['email'], $id)) {
        throw new Exception('Este email já está cadastrado por outro funcionário');
    }
    
    // Configurar dados do funcionário
    $crudFuncionario->setNome1($_POST['primeiro_nome']);
    $crudFuncionario->setNome2($_POST['segundo_nome']);
    $crudFuncionario->setEmail($_POST['email']);
    $crudFuncionario->setTelefone($_POST['telefone']);
    $crudFuncionario->setAcesso($_POST['acesso']);
    
    // Só definir senha se foi fornecida
    if (!empty($_POST['senha'])) {
        $crudFuncionario->setSenha($_POST['senha']);
    }
    
    // Upload da imagem se fornecida
    $imagem_nome = '';
    if (isset($_FILES['imagem_perfil']) && $_FILES['imagem_perfil']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/funcionarios/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $extensao = pathinfo($_FILES['imagem_perfil']['name'], PATHINFO_EXTENSION);
        $imagem_nome = uniqid() . '.' . $extensao;
        $caminho_completo = $upload_dir . $imagem_nome;
        
        if (!move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $caminho_completo)) {
            throw new Exception('Erro ao fazer upload da imagem');
        }
        
        $crudFuncionario->setImagem_perfil($imagem_nome);
    }
    
    // Atualizar funcionário
    if ($crudFuncionario->updateById($id)) {
        echo json_encode(['success' => true, 'message' => 'Funcionário atualizado com sucesso']);
    } else {
        throw new Exception('Erro ao atualizar funcionário');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

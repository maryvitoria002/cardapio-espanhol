<?php
header('Content-Type: application/json');
session_start();

// Verificar se o usuário está logado
if (empty($_SESSION["id"])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

// Incluir controlador de usuário
require_once './controllers/usuario/Crud_usuario.php';

try {
    // Verificar se é uma requisição POST com arquivo
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['foto_perfil'])) {
        throw new Exception('Arquivo não enviado');
    }

    $arquivo = $_FILES['foto_perfil'];
    
    // Verificar se houve erro no upload
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro no upload do arquivo');
    }

    // Verificar tamanho do arquivo (máximo 5MB)
    $tamanho_maximo = 5 * 1024 * 1024; // 5MB
    if ($arquivo['size'] > $tamanho_maximo) {
        throw new Exception('Arquivo muito grande. Máximo 5MB');
    }

    // Verificar tipo do arquivo
    $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_arquivo = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);

    if (!in_array($tipo_arquivo, $tipos_permitidos)) {
        throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG ou GIF');
    }

    // Gerar nome único para o arquivo
    $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $nome_arquivo = 'perfil_' . $_SESSION["id"] . '_' . time() . '.' . $extensao;
    
    // Definir caminho de destino
    $diretorio_destino = './images/usuarios/';
    
    // Criar diretório se não existir
    if (!is_dir($diretorio_destino)) {
        mkdir($diretorio_destino, 0755, true);
    }
    
    $caminho_completo = $diretorio_destino . $nome_arquivo;

    // Mover arquivo para o destino
    if (!move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
        throw new Exception('Erro ao salvar arquivo');
    }

    // Atualizar banco de dados
    $usuario = new Crud_usuario();
    $usuario->setId_usuario($_SESSION["id"]);
    
    // Buscar dados atuais do usuário
    $dadosUsuario = $usuario->read();
    if (!$dadosUsuario) {
        throw new Exception('Usuário não encontrado');
    }

    // Remover foto anterior se existir
    if (!empty($dadosUsuario['imagem_perfil']) && file_exists('./images/usuarios/' . $dadosUsuario['imagem_perfil'])) {
        unlink('./images/usuarios/' . $dadosUsuario['imagem_perfil']);
    }

    // Atualizar no banco
    $usuario->setPrimeiro_nome($dadosUsuario['primeiro_nome']);
    $usuario->setSegundo_nome($dadosUsuario['segundo_nome']);
    $usuario->setEmail($dadosUsuario['email']);
    $usuario->setTelefone($dadosUsuario['telefone'] ?? '');
    $usuario->setImagem_perfil($nome_arquivo);
    $usuario->setData_atualizacao();

    if ($usuario->update()) {
        // Atualizar sessão
        $_SESSION['foto_perfil'] = $nome_arquivo;
        
        echo json_encode([
            'success' => true,
            'message' => 'Foto de perfil atualizada com sucesso!',
            'foto_url' => $caminho_completo
        ]);
    } else {
        // Remover arquivo se falhou no banco
        unlink($caminho_completo);
        throw new Exception('Erro ao atualizar foto no banco de dados');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
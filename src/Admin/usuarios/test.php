<?php
// Teste de conexão e funcionalidades básicas
require_once '../../controllers/usuario/Crud_usuario.php';

try {
    $crudUsuario = new Crud_usuario();
    
    echo "✅ Classe Crud_usuario carregada com sucesso<br>";
    
    // Teste de contagem
    $total = $crudUsuario->count();
    echo "✅ Método count funcionando: $total usuários<br>";
    
    // Teste de listagem
    $usuarios = $crudUsuario->readAll('', '', 0, 5);
    echo "✅ Método readAll funcionando: " . count($usuarios) . " usuários retornados<br>";
    
    // Verificar estrutura de um usuário
    if (!empty($usuarios)) {
        echo "✅ Estrutura do primeiro usuário:<br>";
        echo "<pre>";
        print_r(array_keys($usuarios[0]));
        echo "</pre>";
    }
    
    echo "<br><strong>✅ Sistema de usuários funcionando corretamente!</strong>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>

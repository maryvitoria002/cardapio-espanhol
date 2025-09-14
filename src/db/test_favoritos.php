<?php
require_once 'conection.php';
require_once '../controllers/favorito/Crud_favorito.php';

try {
    echo "Testando conexão e query de favoritos...\n";
    
    $crudFavorito = new Crud_favorito();
    
    // Testar com um usuário que não existe (deve retornar array vazio)
    $favoritos = $crudFavorito->getFavoritosByUsuario(999);
    
    echo "Query executada com sucesso!\n";
    echo "Favoritos encontrados: " . count($favoritos) . "\n";
    
    // Testar com o usuário ID 2 (mencionado no erro original)
    $favoritos2 = $crudFavorito->getFavoritosByUsuario(2);
    echo "Favoritos para usuário 2: " . count($favoritos2) . "\n";
    
    echo "Teste concluído com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
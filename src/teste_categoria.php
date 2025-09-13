?php
require_once './controllers/categoria/Crud_categoria.php';

try {
    echo "Testando Crud_categoria...\n";
    $crudCategoria = new Crud_categoria();
    echo "Instância criada com sucesso.\n";
    
    $categorias = $crudCategoria->read();
    echo "Método read() executado com sucesso.\n";
    echo "Número de categorias encontradas: " . count($categorias) . "\n";
    
    foreach ($categorias as $categoria) {
        echo "- ID: " . $categoria['id_categoria'] . " | Nome: " . $categoria['nome_categoria'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

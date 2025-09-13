<?php
require_once '../controllers/produto/Crud_produto.php';

$crud = new Crud_produto();
$produtos = $crud->readAll();

echo "Produtos encontrados: " . count($produtos) . "\n";

foreach($produtos as $p) {
    if($p['imagem']) {
        echo "ID: " . $p['id_produto'] . " - Nome: " . $p['nome_produto'] . " - Imagem: " . $p['imagem'] . "\n";
    }
}
?>

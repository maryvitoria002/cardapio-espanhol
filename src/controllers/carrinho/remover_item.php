<?php
require_once "../../controllers/carrinho/Crud_carrinho.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

$carrinho = new Crud_carrinho();
$carrinho->setId_carrinho($data['id']); // Usando o método setter correto
$success = $carrinho->delete();

echo json_encode(['success' => $success]);
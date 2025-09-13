<?php
require_once "../../controllers/carrinho/Crud_carrinho.php";

header('Content-Type: application/json');

$id_carrinho = $_POST['id'] ?? null;
$acao = $_POST['acao'] ?? null;

if (!$id_carrinho || !$acao) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$carrinho = new Crud_carrinho();
$carrinho->setId_carrinho($id_carrinho); // Importante: setar o ID antes de atualizar

$novaQuantidade = ($acao === 'aumentar') ? 1 : -1;
$success = $carrinho->atualizarQuantidade($novaQuantidade);

echo json_encode(['success' => $success]);
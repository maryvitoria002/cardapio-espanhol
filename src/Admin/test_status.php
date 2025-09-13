<?php
require_once 'controllers/pedido/Crud_pedido.php';

try {
    $crudPedido = new Crud_pedido();
    
    echo "Testando getCountByStatus():\n";
    $result = $crudPedido->getCountByStatus();
    var_dump($result);
    
    echo "\nConvertendo para array associativo:\n";
    $pedidos_por_status = [];
    foreach ($result as $item) {
        $pedidos_por_status[$item['status_pedido']] = $item['total'];
    }
    var_dump($pedidos_por_status);
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>

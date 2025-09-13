<?php
require_once __DIR__ . "/../../../db/conection.php";

class Pedido extends Database {
    private $id_pedido;
    private $id_usuario;
    private $status_pedido;
    private $data_pedido;
    private $endereco;
    private $data_entrega;
    private $metodo_pagamento;
    
    // Getters
    public function getIdPedido() {
        return $this->id_pedido;
    }
    
    public function getIdUsuario() {
        return $this->id_usuario;
    }
    
    public function getStatusPedido() {
        return $this->status_pedido;
    }
    
    public function getDataPedido() {
        return $this->data_pedido;
    }
    
    public function getEndereco() {
        return $this->endereco;
    }
    
    public function getDataEntrega() {
        return $this->data_entrega;
    }
    
    public function getMetodoPagamento() {
        return $this->metodo_pagamento;
    }
    
    // Setters
    public function setIdPedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }
    
    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }
    
    public function setStatusPedido($status_pedido) {
        $this->status_pedido = $status_pedido;
    }
    
    public function setDataPedido($data_pedido) {
        $this->data_pedido = $data_pedido;
    }
    
    public function setEndereco($endereco) {
        $this->endereco = $endereco;
    }
    
    public function setDataEntrega($data_entrega) {
        $this->data_entrega = $data_entrega;
    }
    
    public function setMetodoPagamento($metodo_pagamento) {
        $this->metodo_pagamento = $metodo_pagamento;
    }
}
?>

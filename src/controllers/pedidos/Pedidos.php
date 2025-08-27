<?php 

require_once "../../db/conection.php";

class Pedidos {
    protected $tabela = "pedidos";
    private $id_pedido;
    private $email;
    private $endereco;
    private $modo_p;
    private $status_pagamento;
    private $data_pedido;
    private $status_pedido;
    private $motivo_cancelamento;
    private $nota;

    // Getters
    public function getIdpedido() {
        return $this->id_pedido;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function getModop () {
        return $this->modo_p;
    }

    public function getStatuspagamento () {
        return $this->status_pagamento;
    }

    public function getDatapedido () {
        return $this->data_pedido;
    }

    public function getStatuspedido () {
        return $this->status_pedido;
    }

    public function getMotivocancelamento () {
        return $this->motivo_cancelamento;
    }

    public function getNota () {
        return $this->nota;
    }

    // Setters

    public function setIdpedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setEndereco($endereco) {
        $this->endereco = $endereco;
    }

    public function setModop($modo_p) {
        $this->modo_p = $modo_p;
    }

    public function setStatuspagamento($status_pagamento) {
        $this->status_pagamento = $status_pagamento;
    }

    public function setDatapedido($data_pedido) {
        $this->data_pedido = $data_pedido;
    }

    public function setStatuspedido($status_pedido) {
        $this->status_pedido = $status_pedido;
    }

    public function setMotivocancelamento($motivo_cancelamento) {
        $this->motivo_cancelamento = $motivo_cancelamento;
    }

    public function setNota($nota) {
        $this->nota = $nota;
    }
}
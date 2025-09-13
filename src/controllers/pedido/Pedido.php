<?php 

require_once __DIR__ . "/../../db/conection.php";

class Pedido {
    protected $tabela = "pedido";
    private $id_pedido;
    private $endereco;
    private $modo_pagamento;
    private $status_pagamento;
    private $data_pedido;
    private $status_pedido;
    private $motivo_cancelamento;
    private $nota;
    private $id_usuario;

    public function getId_pedido() {
        return $this->id_pedido;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function getModo_pagamento() {
        return $this->modo_pagamento;
    }

    public function getStatus_pagamento () {
        return $this->status_pagamento;
    }

    public function getData_pedido () {
        return $this->data_pedido;
    }

    public function getStatus_pedido () {
        return $this->status_pedido;
    }

    public function getMotivo_cancelamento () {
        return $this->motivo_cancelamento;
    }

    public function getNota () {
        return $this->nota;
    }

    public function getId_usuario(){
        return $this->id_usuario;
    }

    public function setId_pedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function setId_usuario($id_usuario){
        $this->id_usuario = $id_usuario;
    }

    public function setEndereco($endereco) {
        $this->endereco = $endereco;
    }

    public function setModo_pagamento($modo_pagamento) {
        $this->modo_pagamento = $modo_pagamento;
    }

    public function setStatus_pagamento($status_pagamento) {
        $this->status_pagamento = $status_pagamento;
    }

    public function setData_pedido($data_pedido) {
        $this->data_pedido = $data_pedido;
    }

    public function setStatus_pedido($status_pedido) {
        $this->status_pedido = $status_pedido;
    }

    public function setMotivo_cancelamento($motivo_cancelamento) {
        $this->motivo_cancelamento = $motivo_cancelamento;
    }

    public function setNota($nota) {
        $this->nota = $nota;
    }

}
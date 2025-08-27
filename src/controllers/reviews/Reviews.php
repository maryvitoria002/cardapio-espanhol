<?php 

require_once "../../db/conection.php";

class Reviews {
    protected $tabela = "reviews";
    private $id_review;
    private $email;
    private $id_pedido;
    private $nota;
    private $texto_review;
    private $data_review;
    private $status;
    private $resposta;

    // Getters
    public function getIdreview() {
        return $this->id_review;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getIdpedido() {
        return $this->id_pedido;
    }

    public function getNota() {
        return $this->nota;
    }

    public function getTextoreview() {
        return $this->texto_review;
    }

    public function getDatareview() {
        return $this->data_review;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getResposta() {
        return $this->resposta;
    }

    // Setters

    public function setIdreview($id_review) {
        $this->id_review = $id_review;
    }

    public function setIdCliente($email) {
        $this->email = $email;
    }

    public function setIdpedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function setNota($nota) {
        $this->nota = $nota;
    }

    public function setTextoreview($texto_review) {
        $this->texto_review = $texto_review;
    }

    public function setDatareview() {
        $this->data_review = date('Y-m-d H:i:s');
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setResposta($resposta) {
        $this->resposta = $resposta;
    }
}
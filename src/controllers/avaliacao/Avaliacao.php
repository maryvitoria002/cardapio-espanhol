<?php 

require_once __DIR__ . "/../../db/conection.php";

class Avaliacao extends Database{
    protected $tabela = "avaliacao";
    private $id_avaliacao;
    private $nota;
    private $texto_avaliacao;
    private $data_avaliacao;
    private $status;
    private $resposta;
    private $id_pedido;
    private $id_usuario;

    // Getters
    public function getId_avaliacao() {
        return $this->id_avaliacao;
    }

    public function getNota() {
        return $this->nota;
    }

    public function getTexto_avaliacao() {
        return $this->texto_avaliacao;
    }

    public function getData_avaliacao() {
        return $this->data_avaliacao;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getResposta() {
        return $this->resposta;
    }

    public function getId_pedido() {
        return $this->id_pedido;
    }

    public function getId_usuario() {
        return $this->id_usuario;
    }

    // Setters

    public function setId_avaliacao($id_avaliacao) {
        $this->id_avaliacao = $id_avaliacao;
    }

    public function setNota($nota) {
        $this->nota = $nota;
    }

    public function setTexto_avaliacao($texto_avaliacao) {
        $this->texto_avaliacao = $texto_avaliacao;
    }

    public function setData_avaliacao() {
        $this->data_avaliacao = date('Y-m-d H:i:s');
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setResposta($resposta) {
        $this->resposta = $resposta;
    }

    public function setId_pedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function setId_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }
}
<?php
require_once __DIR__ . "/../../../db/conection.php";

class Avaliacao extends Database {
    private $id_avaliacao;
    private $id_usuario;
    private $nota;
    private $texto_avaliacao;
    private $data_avaliacao;
    private $status;
    
    // Getters
    public function getIdAvaliacao() {
        return $this->id_avaliacao;
    }
    
    public function getIdUsuario() {
        return $this->id_usuario;
    }
    
    public function getNota() {
        return $this->nota;
    }
    
    public function getTextoAvaliacao() {
        return $this->texto_avaliacao;
    }
    
    public function getDataAvaliacao() {
        return $this->data_avaliacao;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    // Setters
    public function setIdAvaliacao($id_avaliacao) {
        $this->id_avaliacao = $id_avaliacao;
    }
    
    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }
    
    public function setNota($nota) {
        $this->nota = $nota;
    }
    
    public function setTextoAvaliacao($texto_avaliacao) {
        $this->texto_avaliacao = $texto_avaliacao;
    }
    
    public function setDataAvaliacao($data_avaliacao) {
        $this->data_avaliacao = $data_avaliacao;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
}
?>

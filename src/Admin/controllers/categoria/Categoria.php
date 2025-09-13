<?php
require_once __DIR__ . "/../../../db/conection.php";

class Categoria extends Database {
    private $id_categoria;
    private $nome_categoria;
    private $descricao;
    
    // Getters
    public function getIdCategoria() {
        return $this->id_categoria;
    }
    
    public function getNomeCategoria() {
        return $this->nome_categoria;
    }
    
    public function getDescricao() {
        return $this->descricao;
    }
    
    // Setters
    public function setIdCategoria($id_categoria) {
        $this->id_categoria = $id_categoria;
    }
    
    public function setNomeCategoria($nome_categoria) {
        $this->nome_categoria = $nome_categoria;
    }
    
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }
}
?>

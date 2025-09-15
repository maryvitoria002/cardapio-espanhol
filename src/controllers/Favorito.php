<?php
require_once __DIR__ . '/../../db/conection.php';

class Favorito {
    private $id_favorito;
    private $id_usuario;
    private $id_produto;
    private $data_criacao;

    public function __construct($id_usuario = null, $id_produto = null) {
        $this->id_usuario = $id_usuario;
        $this->id_produto = $id_produto;
    }

    // Getters
    public function getIdFavorito() {
        return $this->id_favorito;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getIdProduto() {
        return $this->id_produto;
    }

    public function getDataCriacao() {
        return $this->data_criacao;
    }

    // Setters
    public function setIdFavorito($id_favorito) {
        $this->id_favorito = $id_favorito;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setIdProduto($id_produto) {
        $this->id_produto = $id_produto;
    }

    public function setDataCriacao($data_criacao) {
        $this->data_criacao = $data_criacao;
    }
}
?>
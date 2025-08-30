<?php 

require_once __DIR__ . "/../../db/conection.php";

class Carrinho extends Database{
    protected $tabela = "carrinho";
    private $id_carrinho;
    private $quantidade;
    private $id_produto;
    private $id_usuario;

    // Getters
    public function getId_carrinho() {
        return $this->id_carrinho;
    }

    public function getId_produto() {
        return $this->id_produto;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    public function getId_usuario() {
        return $this->id_usuario;
    }

    // Setters

    public function setId_carrinho($id_carrinho) {
        $this->id_carrinho = $id_carrinho;
    }

    public function setId_usuario($id_usuario) {
        $this->id_usuario= $id_usuario;
    }

    public function setId_produto($id_produto) {
        $this->id_produto= $id_produto;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }
}
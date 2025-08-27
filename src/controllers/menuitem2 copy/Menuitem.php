<?php 

require_once "../../db/conection.php";

class Carrinho {
    protected $tabela = "carrinho";
    private $id;
    private $email;
    private $id_produto;
    private $quantidade;

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getIdProduto() {
        return $this->id_produto;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    // Setters

    public function setId($id) {
        $this->id = $id;
    }

    public function setIdCliente($email) {
        $this->email = $email;
    }

    public function setIdProduto($id_produto) {
        $this->id_produto = $id_produto;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }
}
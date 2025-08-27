<?php 

require_once "../../db/conection.php";

class Carrinho {
    protected $tabela = "carrinho";
    private $id;
    private $email;
    private $id_item;
    private $quantidade;

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getIditem() {
        return $this->id_item;
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

    public function setIditem($id_item) {
        $this->id_item = $id_item;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }
}
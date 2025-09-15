<?php 

require_once __DIR__ . "/../db/conection.php";

class Produto_pedido {
    protected $tabela = "produto_pedido";
    private $id_produto_pedido;
    private $quantidade;
    private $preco;
    private $id_produto;
    private $id_pedido;
   

    // Getters
    public function getId_produto_pedido() {
        return $this->id_produto_pedido;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }
    
    public function getPreco() {
        return $this->preco;
    }
    
    public function getId_produto() {
        return $this->id_produto;
    }

    public function getId_pedido() {
        return $this->id_pedido;
    }

    // Setters

    public function setId_produto_pedido($id_produto_pedido) {
        $this->id_produto_pedido = $id_produto_pedido;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }

    public function setPreco($preco) {
        $this->preco = $preco;
    }

    public function setId_produto($id_produto) {
        $this->id_produto = $id_produto;
    }

    public function setId_pedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }
}
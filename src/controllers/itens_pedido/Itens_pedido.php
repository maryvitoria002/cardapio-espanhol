<?php 

require_once "../../db/conection.php";

class Itens_pedido {
    protected $tabela = "itens_pedido";
    private $id;
    private $id_pedido;
    private $nomeItem;
    private $imagem;
    private $quantidade;
    private $preco;
   

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getIdpedido() {
        return $this->id_pedido;
    }

    public function getNomeitem() {
        return $this->nomeItem;
    }

    public function getImagem() {
        return $this->imagem;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    public function getPreco() {
        return $this->preco;
    }

    // Setters

    public function setId($id) {
        $this->id = $id;
    }

    public function setIdpedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function setNomeitem($nomeItem) {
        $this->nomeItem = $nomeItem;
    }

    public function setImagem($imagem) {
        $this->imagem = $imagem;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }

    public function setPreco($preco) {
        $this->preco = $preco;
    }
}
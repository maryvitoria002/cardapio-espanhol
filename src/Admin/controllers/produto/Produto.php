<?php
require_once __DIR__ . "/../../../db/conection.php";

class Produto extends Database {
    private $id_produto;
    private $nome_produto;
    private $preco;
    private $descricao;
    private $imagem;
    private $estoque;
    private $id_categoria;
    
    // Getters
    public function getIdProduto() {
        return $this->id_produto;
    }
    
    public function getNomeProduto() {
        return $this->nome_produto;
    }
    
    public function getPreco() {
        return $this->preco;
    }
    
    public function getDescricao() {
        return $this->descricao;
    }
    
    public function getImagem() {
        return $this->imagem;
    }
    
    public function getEstoque() {
        return $this->estoque;
    }
    
    public function getIdCategoria() {
        return $this->id_categoria;
    }
    
    // Setters
    public function setIdProduto($id_produto) {
        $this->id_produto = $id_produto;
    }
    
    public function setNomeProduto($nome_produto) {
        $this->nome_produto = $nome_produto;
    }
    
    public function setPreco($preco) {
        $this->preco = $preco;
    }
    
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }
    
    public function setImagem($imagem) {
        $this->imagem = $imagem;
    }
    
    public function setEstoque($estoque) {
        $this->estoque = $estoque;
    }
    
    public function setIdCategoria($id_categoria) {
        $this->id_categoria = $id_categoria;
    }
}
?>

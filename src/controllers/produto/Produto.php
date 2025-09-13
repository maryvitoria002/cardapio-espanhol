<?php 

require_once __DIR__ . "/../../db/conection.php";

class Produto {
    protected $tabela = "produto";
    private $id_produto;
    private $nome_produto;
    private $preco;
    private $estoque;
    private $status;
    private $descricao;
    private $imagem;
    private $data_criacao;
    private $data_atualizacao;
    private $eh_popular;
    private $id_categoria;

    // Getters
    public function getId_produto() {
        return $this->id_produto;
    }

    public function getNome_produto():string {
        return $this->nome_produto;
    }

    public function getPreco():float {
        return $this->preco;
    }

    public function getEstoque():int {
        return $this->estoque;
    }

    public function getStatus():string {
        return $this->status;
    }

    public function getDescricao():string {
        return $this->descricao ?? '';
    }

    public function getImagem():string {
        return $this->imagem ?? '';
    }

    public function getData_criacao():string {
        return $this->data_criacao ?? "";
    }

    public function getData_atualizacao():string {
        return $this->data_atualizacao?? "";
    }

    public function getEh_popular():bool {
        return $this->eh_popular;
    }

    public function getId_categoria():int {
        return $this->id_categoria;
    }

    // Setters

    public function setId_produto($id_produto) {
        $this->id_produto = $id_produto;
    }

    public function setNome_produto($nome_produto) {
        $this->nome_produto = $nome_produto;
    }

    public function setPreco($preco) {
        $this->preco = $preco;
    }

    public function setEstoque($estoque) {
        $this->estoque = $estoque;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function setImagem($imagem) {
        $this->imagem = $imagem;
    }

    public function setData_criacao() {
        $this->data_criacao = date('Y-m-d H:i:s');
    }

    public function setData_atualizacao($data_atualizacao = null) {
        $this->data_atualizacao= $data_atualizacao ?? date('Y-m-d H:i:s');
    }

    public function setEh_popular($eh_popular) {
        $this->eh_popular = $eh_popular;
    }

    public function setId_categoria($id_categoria) {
        $this->id_categoria = $id_categoria;
    }
    
}
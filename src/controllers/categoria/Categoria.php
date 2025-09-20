<?php 
require_once __DIR__ . "/../../db/conection.php";

class Categoria extends Database{
    protected $tabela = "categoria";
    private $id_categoria;
    private $nome_categoria;
    private $descricao;
    private $data_criacao;

    public function getId_categoria():?string {
        return $this->id_categoria;
    }
    
    public function setId_categoria($id_categoria) {
        $this->id_categoria = $id_categoria;
    }

    public function getNome_categoria():?string {
        return $this->nome_categoria;
    }

    public function setNome_categoria($nome_categoria) {
        $this->nome_categoria = $nome_categoria;
    }
    
    // Alias para compatibilidade
    public function setNomeCategoria($nome_categoria) {
        $this->setNome_categoria($nome_categoria);
    }

    public function getDescricao():?string {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getData_criacao():string {
        return $this->data_criacao ?? "";
    }

    public function setData_criacao() {
        $this->data_criacao = date('Y-m-d H:i:s');
    }

}
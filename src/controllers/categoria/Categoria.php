<?php 
require_once __DIR__ . "/../../db/conection.php";

class Categoria extends Database{
    protected $tabela = "categoria";
    private $id_categoria;
    private $nome_categoria;
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

    public function setNome_categora($nome_categoria) {
        $this->nome_categoria= $nome_categoria;
    }

    public function getData_criacao():string {
        return $this->data_criacao ?? "";
    }

    public function setData_criacao() {
        $this->data_criacao = date('Y-m-d H:i:s');
    }

}
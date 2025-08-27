<?php 
require_once "./db/conection.php";

class Categoria extends Database{
    protected $tabela = "categoria";
    private $id_categ;
    private $nomeCateg;
    private $dataCriacao;

    public function getIdcateg():string {
        return $this->id_categ;
    }
    
    public function setIdcateg($id_categ) {
        $this->id_categ = $id_categ;
    }

    public function getNomecateg():string {
        return $this->nomeCateg;
    }

    public function setNomecateg($nomeCateg) {
        $this->nomeCateg = $nomeCateg;
    }

    public function getData():string {
        return $this->dataCriacao ?? "";
    }

    public function setData() {
        $this->dataCriacao = date('Y-m-d H:i:s');
    }

}
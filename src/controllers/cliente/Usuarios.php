<?php 
require_once "./db/conection.php";

class Usuarios extends Database{
    protected $tabela = "usuarios";
    private $email;
    private $primeiro_nome;
    private $segun_nome;
    private $telefone;
    private $senha;
    private $dataCriacao;
    private $imagem_perfil;


    public function getEmail():string {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getNome1():string {
        return $this->primeiro_nome;
    }

    public function setNome1($primei_nome) {
        $this->primeiro_nome = $primei_nome;
    }

    public function getNome2():string {
        return $this->segun_nome ?? '';
    }

    public function setNome2($segun_nome) {
        $this->segun_nome = $segun_nome;
    }
    
    public function getTelefone():string {
        return $this->telefone;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getSenha():string {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getData():string {
        return $this->dataCriacao ?? "";
    }

    public function setData() {
        $this->dataCriacao = date('Y-m-d H:i:s');
    }

    public function getImagem():string {
        return $this->imagem_perfil ?? '';
    }

    public function setImagem($imagem_perfil) {
        $this->imagem_perfil = $imagem_perfil;
    }
}
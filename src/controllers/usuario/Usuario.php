<?php 
require_once __DIR__ . "/../../db/conection.php";

class Usuario extends Database{
    protected $tabela = "usuario";
    private $id_usuario;
    private $email;
    private $primeiro_nome;
    private $segundo_nome;
    private $telefone;
    private $senha;
    private $data_criacao;
    private $data_atualizacao;
    private $imagem_perfil;


    public function getId_usuario() {
        return $this->id_usuario;
    }
    
    public function setId_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getEmail():string {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPrimeiro_nome():string {
        return $this->primeiro_nome;
    }

    public function setPrimeiro_nome($primeiro_nome) {
        $this->primeiro_nome = $primeiro_nome;
    }

    public function getSegundo_nome():string {
        return $this->segundo_nome ?? '';
    }

    public function setSegundo_nome($segundo_nome) {
        $this->segundo_nome = $segundo_nome;
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

    public function getData_criacao():string {
        return $this->data_criacao ?? "";
    }

    public function setData_criacao() {
        $this->data_criacao = date('Y-m-d H:i:s');
    }

    public function getData_atualizacao():string {
        return $this->data_atualizacao ?? "";
    }

    public function setData_atualizacao() {
        $this->data_atualizacao = date('Y-m-d H:i:s');
    }

    public function getImagem_perfil():string {
        return $this->imagem_perfil ?? '';
    }

    public function setImagem_perfil($imagem_perfil) {
        $this->imagem_perfil = $imagem_perfil;
    }
}
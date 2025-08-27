<?php 
require_once "./db/conection.php";

class Staff extends Database{
    protected $tabela = "staff";
    private $id;
    private $primeiro_nome;
    private $segundo_nome;
    private $email;
    private $telefone;
    private $acesso;
    private $senha;
    private $criadoEm;
    private $atualizadoEm;
    private $imagem_perfil;

    // Getters e Setters

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNome1() {
        return $this->primeiro_nome;
    }

    public function setNome1($primeiro_nome) {
        $this->primeiro_nome = $primeiro_nome;
    }

    public function getNome2() {
        return $this->segundo_nome ?? '';
    }

    public function setNome2($segundo_nome) {
        $this->segundo_nome = $segundo_nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getAcesso() {
        return $this->acesso;
    }

    public function setAcesso($acesso) {
        $this->acesso = $acesso;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getCriadoEm() {
        return $this->criadoEm ?? "";
    }

    public function setCriadoEm($criadoEm = null) {
        $this->criadoEm = $criadoEm ?? date('Y-m-d H:i:s');
    }

    public function getAtualizadoEm() {
        return $this->atualizadoEm ?? "";
    }

    public function setAtualizadoEm($atualizadoEm = null) {
        $this->atualizadoEm = $atualizadoEm ?? date('Y-m-d H:i:s');
    }

    public function getImagem_perfil() {
        return $this->imagem_perfil ?? '';
    }

    public function setImagem_perfil($imagem_perfil) {
        $this->imagem_perfil = $imagem_perfil;
    }

}
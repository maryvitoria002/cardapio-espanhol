<?php 
require_once __DIR__ . "/../db/conection.php";

class Funcionario extends Database{
    protected $tabela = "funcionario";
    private $id_funcionario;
    private $primeiro_nome;
    private $segundo_nome;
    private $email;
    private $telefone;
    private $acesso;
    private $senha;
    private $data_criacao;
    private $data_atualizacao;
    private $imagem_perfil;

    // Getters e Setters

    public function getId_funcionario() {
        return $this->id_funcionario;
    }

    public function setId($id_funcionario) {
        $this->id_funcionario = $id_funcionario;
    }

    public function getPrimeiro_nome() {
        return $this->primeiro_nome;
    }

    public function setPrimeiro_nome($primeiro_nome) {
        $this->primeiro_nome = $primeiro_nome;
    }
    
    // Alias para compatibilidade
    public function getNome1() {
        return $this->primeiro_nome;
    }

    public function setNome1($primeiro_nome) {
        $this->primeiro_nome = $primeiro_nome;
    }

    public function getSegundo_nome() {
        return $this->segundo_nome ?? '';
    }

    public function setSegundo_nome($segundo_nome) {
        $this->segundo_nome = $segundo_nome;
    }
    
    // Alias para compatibilidade
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

    public function getData_criacao() {
        return $this->data_criacao ?? "";
    }

    public function setData_criacao($data_criacao = null) {
        $this->data_criacao = $data_criacao ?? date('Y-m-d H:i:s');
    }
    
    // Alias para compatibilidade
    public function getCriadoEm() {
        return $this->data_criacao ?? "";
    }

    public function setCriadoEm($data_criacao = null) {
        $this->data_criacao = $data_criacao ?? date('Y-m-d H:i:s');
    }

    public function getData_atualizacao() {
        return $this->data_atualizacao?? "";
    }

    public function setData_atualizacao($data_atualizacao = null) {
        $this->data_atualizacao= $data_atualizacao ?? date('Y-m-d H:i:s');
    }

    public function getImagem_perfil() {
        return $this->imagem_perfil ?? '';
    }

    public function setImagem_perfil($imagem_perfil) {
        $this->imagem_perfil = $imagem_perfil;
    }

}
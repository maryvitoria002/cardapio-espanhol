<?php 
require_once "./db/conection.php";

class Cliente extends Database{
    protected $tabela = "clientes";
    private $cpf;
    private $nome_completo;
    private $nascimento;
    private $email;
    private $senha;
    private $telefone;
    private $preferencias;
    private $nivel_acesso;


    public function getCpf():string {
        return $this->cpf;
    }
    
    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function getNome():string {
        return $this->nome_completo;
    }

    public function setNome($nome_completo) {
        $this->nome_completo = $nome_completo;
    }

    public function getNat():string {
        return $this->nascimento;
    }

    public function setNat($nascimento) {
        $this->nascimento = $nascimento;
    }

    public function getEmail():string {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getSenha():string {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getTelefone():string {
        return $this->telefone;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getPreferencias():string {
        return $this->preferencias;
    }

    public function setPreferencias($preferencias) {
        $this->preferencias = $preferencias;
    }

    public function getNivelAcesso():string {
        return $this->nivel_acesso;
    }

    public function setNivelAcesso($nivel_acesso) {
        $this->nivel_acesso = $nivel_acesso;
    }
}
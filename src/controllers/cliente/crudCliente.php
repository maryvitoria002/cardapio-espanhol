<?php 
require_once "Usuarios.php";

class CrudCliente extends Usuarios{
    
    public function insert(){
        $email = $this->getEmail();
        $primeiro_nome = $this->getNome1();
        $segun_nome = $this->getNome2();
        $telefone = $this->getTelefone();
        $senha = $this->getSenha();
        $dataCriacao = $this->getData();
        $imagem_perfil = $this->getImagem();
        $sql = "INSERT INTO `{$this->tabela}` (email, primei_nome, segun_nome, telefone, senha, dataCriacao, imagem_perfil) VALUES (:email, :primei_nome, :segun_nome, :telefone, :senha, :dataCriacao, :imagem_perfil)";
        
        // Create database instance and prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":primei_nome", $primeiro_nome, PDO::PARAM_STR);
        $stmt->bindParam(":segun_nome", $segun_nome, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stmt->bindParam(":dataCriacao", $dataCriacao, PDO::PARAM_STR);
        $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href = './login.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }

    public function login() {
        $email = $this->getEmail();
        $senha = $this->getSenha();
        $sql = "SELECT * FROM `{$this->tabela}` WHERE email = :email AND senha = :senha";
        
        // Create database instance and prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                session_start();
                $_SESSION["nome1"] = $result["primei_nome"];
                $_SESSION["nome2"] = $result["segun_nome"];
                $_SESSION["id"] = $result["email"];
                header("Location: ./index.php");
                exit();
            } else {
                echo "<script>alert('Usuário ou senha inválidos')</script>";
            }
        } catch (PDOException $e){
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
        
    }
}



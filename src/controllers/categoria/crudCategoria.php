<?php 
require_once "Categoria.php";

class CrudCliente extends Categoria{

    // CRUD CRIAR (CREATE)
    
    public function create(){
        $id_categ = $this->getid_categ();
        $nomeCateg = $this->getNomecateg();
        $segun_nome = $this->getNome2();
        $telefone = $this->getTelefone();
        $senha = $this->getSenha();
        $dataCriacao = $this->getData();
        $imagem_perfil = $this->getImagem();
        $sql = "INSERT INTO `{$this->tabela}` (id_categ, primei_nome, segun_nome, telefone, senha, dataCriacao, imagem_perfil) VALUES (:id_categ, :primei_nome, :segun_nome, :telefone, :senha, :dataCriacao, :imagem_perfil)";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_categ", $id_categ, PDO::PARAM_STR);
        $stmt->bindParam(":primei_nome", $nomeCateg, PDO::PARAM_STR);
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

    // CRUD LER (READ)
    public function read(){;
        $id_categ = $this->getid_categ();
        $sql = "SELECT * FROM `{$this->tabela}` WHERE id_categ = :id_categ";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_categ", $id_categ, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }

    public function login() {
        $id_categ = $this->getid_categ();
        $senha = $this->getSenha();
        $sql = "SELECT * FROM `{$this->tabela}` WHERE id_categ = :id_categ AND senha = :senha";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_categ", $id_categ, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                session_start();
                $_SESSION["nome1"] = $result["primei_nome"];
                $_SESSION["nome2"] = $result["segun_nome"];
                $_SESSION["id"] = $result["id_categ"];
                header("Location: ./index.php");
                exit();
            } else {
                echo "<script>alert('Usuário ou senha inválidos')</script>";
            }
        } catch (PDOException $e){
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
        
    }
    
    // CRUD LER (UPDATE)
    public function update(){
        $id_categ = $this->getid_categ();
        $primeiro_nome = $this->getNome1();
        $segun_nome = $this->getNome2();
        $telefone = $this->getTelefone();
        $senha = $this->getSenha();
        $imagem_perfil = $this->getImagem();
        $sql = "UPDATE `{$this->tabela}` SET primei_nome = :primei_nome, segun_nome = :segun_nome, telefone = :telefone, senha = :senha, imagem_perfil = :imagem_perfil WHERE id_categ = :id_categ";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_categ", $id_categ, PDO::PARAM_STR);
        $stmt->bindParam(":primei_nome", $primeiro_nome, PDO::PARAM_STR);
        $stmt->bindParam(":segun_nome", $segun_nome, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            echo "<script>alert('Atualização realizada com sucesso!'); window.location.href = './perfil.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }

    // CRUD DE APAGAR

    public function delete(){
        $id_categ = $this->getid_categ();
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_categ = :id_categ";
        
        // Create database instance and prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_categ", $id_categ, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            echo "<script>alert('Conta deletada com sucesso!'); window.location.href = './login.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
}



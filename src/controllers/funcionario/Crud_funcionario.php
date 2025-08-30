<?php 

require_once "./Staff.php";

class CrudStaff extends Staff{
 
    // CRUD CRIAR (CREATE)

    public function create(){
        $nome1 = $this->getNome1();
        $nome2 = $this->getNome2();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $acesso = $this->getAcesso();
        $senha = $this->getSenha();
        $this->setCriadoEm(); // Vai definir a data atual
        $criadoEm = $this->getCriadoEm();
        $imagem_perfil = $this->getImagem_perfil();
        $sql = "INSERT INTO `{$this->tabela}` (primeiro_nome, segundo_nome, email, telefone, acesso, senha, criadoEm, imagem_perfil) VALUES (:primeiro_nome, :segundo_nome, :email, :telefone, :acesso, :senha, :criadoEm, :imagem_perfil)";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":primeiro_nome", $nome1, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $nome2, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":acesso", $acesso, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stmt->bindParam(":criadoEm", $criadoEm, PDO::PARAM_STR);
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
        $email = $this->getEmail();
        $sql = "SELECT * FROM `{$this->tabela}` WHERE email = :email";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }

    public function login() {
        $email = $this->getEmail();
        $senha = $this->getSenha();
        $sql = "SELECT * FROM `{$this->tabela}` WHERE email = :email AND senha = :senha";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }

    // CRUD ATUALIZAR (UPDATE)
    public function update(){
        $id = $this->getId();
        $nome1 = $this->getNome1();
        $nome2 = $this->getNome2();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $acesso = $this->getAcesso();
        $senha = $this->getSenha();
        $this->setAtualizadoEm(); // Vai definir a data atual
        $atualizadoEm = $this->getAtualizadoEm();
        $imagem_perfil = $this->getImagem_perfil();
        $sql = "UPDATE `{$this->tabela}` SET primeiro_nome = :primeiro_nome, segundo_nome = :segundo_nome, email = :email, telefone = :telefone, acesso = :acesso, senha = :senha, atualizadoEm = :atualizadoEm, imagem_perfil = :imagem_perfil WHERE id = :id";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);
        $stmt->bindParam(":primeiro_nome", $nome1, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $nome2, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":acesso", $acesso, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stmt->bindParam(":atualizadoEm", $atualizadoEm, PDO::PARAM_STR);
        $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);

        try {
            $stmt->execute();
            echo "<script>alert('Atualização realizada com sucesso!'); window.location.href = './perfil.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }

    // CRUD DELETAR (DELETE)
    public function delete(){
        $id = $this->getId();
        $sql = "DELETE FROM `{$this->tabela}` WHERE id = :id";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);

        try {
            $stmt->execute();
            echo "<script>alert('Conta deletada com sucesso!'); window.location.href = '../home/index.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
}
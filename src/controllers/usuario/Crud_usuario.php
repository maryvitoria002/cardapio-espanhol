<?php 

require_once __DIR__ . '\Usuario.php';


class Crud_usuario extends Usuario{

    /*
    CREATE TABLE `usuario` (
        `id_usuario` int NOT NULL AUTO_INCREMENT,
        `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
        `primeiro_nome` varchar(225) COLLATE utf8mb4_general_ci NOT NULL,
        `segundo_nome` varchar(225) COLLATE utf8mb4_general_ci NOT NULL,
        `telefone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
        `senha` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
        `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `data_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `imagem_perfil` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'default.jpg',
        PRIMARY KEY (`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    */
        
    // CRUD CRIAR (CREATE)
    public function create(){
        $email = $this->getEmail();
        $primeiro_nome = $this->getPrimeiro_nome();
        $segundo_nome = $this->getSegundo_nome();
        $telefone = $this->getTelefone();
        $senha = $this->getSenha();
        $data_criacao = $this->getData_criacao();
        $data_atualizacao = $this->getData_atualizacao();
        $imagem_perfil = $this->getImagem_perfil();
        $sql = "INSERT INTO `{$this->tabela}` (email, primeiro_nome, segundo_nome, telefone, senha, data_criacao, data_atualizacao, imagem_perfil) VALUES (:email, :primeiro_nome, :segundo_nome, :telefone, :senha, :data_criacao, :data_atualizacao, :imagem_perfil)";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":primeiro_nome", $primeiro_nome, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $segundo_nome, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stmt->bindParam(":data_criacao", $data_criacao, PDO::PARAM_STR);
        $stmt->bindParam(":data_atualizacao", $data_atualizacao, PDO::PARAM_STR);
        $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href = '../login.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }

    // CRUD LER (READ)
    public function read(){;
        $id_usuario = $this->getId_usuario();
        $sql = "SELECT * FROM `{$this->tabela}` WHERE id_usuario = :id_usuario";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);

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
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                session_start();
                $_SESSION["primeiro_nome"] = $result["primeiro_nome"];
                $_SESSION["segundo_nome"] = $result["segundo_nome"];
                $_SESSION["id"] = $result["id_usuario"];
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
        $id_usuario = $this->getId_usuario();
        $primeiro_nome = $this->getPrimeiro_nome();
        $segundo_nome = $this->getSegundo_nome();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $data_atualizacao = $this->getData_atualizacao();
        $imagem_perfil = $this->getImagem_perfil();
        $sql = "UPDATE `{$this->tabela}` SET primeiro_nome = :primeiro_nome, segundo_nome = :segundo_nome, email = :email, telefone = :telefone, data_atualizacao = :data_atualizacao, imagem_perfil = :imagem_perfil WHERE id_usuario = :id_usuario)";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":primeiro_nome", $primeiro_nome, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $segundo_nome, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":data_atualizacao", $data_atualizacao, PDO::PARAM_STR);
        $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            echo "<script>alert('Dados atualizados'); window.location.href = './index.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }

    // CRUD DE APAGAR

    public function delete(){
        $id_usuario = $this->getId_usuario();
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_usuario = :id_usuario";
        
        // Create database instance and prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            echo "<script>alert('Conta deletada com sucesso!'); window.location.href = './login.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
}



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
    public function read(){
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
        $telefone = $this->getTelefone();
        $imagem_perfil = $this->getImagem_perfil();
        $data_atualizacao = $this->getData_atualizacao();
        
        // Incluir imagem_perfil apenas se estiver definida
        if (!empty($imagem_perfil)) {
            $sql = "UPDATE `{$this->tabela}` SET primeiro_nome = :primeiro_nome, segundo_nome = :segundo_nome, telefone = :telefone, imagem_perfil = :imagem_perfil, data_atualizacao = :data_atualizacao WHERE id_usuario = :id_usuario";
        } else {
            $sql = "UPDATE `{$this->tabela}` SET primeiro_nome = :primeiro_nome, segundo_nome = :segundo_nome, telefone = :telefone, data_atualizacao = :data_atualizacao WHERE id_usuario = :id_usuario";
        }
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":primeiro_nome", $primeiro_nome, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $segundo_nome, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        if (!empty($imagem_perfil)) {
            $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);
        }
        $stmt->bindParam(":data_atualizacao", $data_atualizacao, PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            return true;
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
    
    // Métodos adicionais para o admin
    public function readAll($search = '', $status = '', $offset = 0, $limit = 10) {
        try {
            $sql = "SELECT * FROM `{$this->tabela}`";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(primeiro_nome LIKE :search OR segundo_nome LIKE :search OR email LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY data_criacao DESC LIMIT :offset, :limit";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar usuários: " . $e->getMessage());
        }
    }
    
    public function count($search = '', $status = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}`";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(primeiro_nome LIKE :search OR segundo_nome LIKE :search OR email LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $database = new Database();
            $conexao = $database->getInstance();
            $stmt = $conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar usuários: " . $e->getMessage());
        }
    }
    
    public function readById($id) {
        try {
            $sql = "SELECT * FROM `{$this->tabela}` WHERE id_usuario = :id";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar usuário: " . $e->getMessage());
        }
    }
    
    public function createUser($dados) {
        try {
            $sql = "INSERT INTO `{$this->tabela}` (primeiro_nome, segundo_nome, email, senha, telefone, endereco, data_criacao) 
                    VALUES (:primeiro_nome, :segundo_nome, :email, :senha, :telefone, :endereco, NOW())";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(":primeiro_nome", $dados['primeiro_nome'], PDO::PARAM_STR);
            $stmt->bindParam(":segundo_nome", $dados['segundo_nome'], PDO::PARAM_STR);
            $stmt->bindParam(":email", $dados['email'], PDO::PARAM_STR);
            $stmt->bindParam(":senha", $dados['senha'], PDO::PARAM_STR);
            $stmt->bindParam(":telefone", $dados['telefone'], PDO::PARAM_STR);
            $stmt->bindParam(":endereco", $dados['endereco'], PDO::PARAM_STR);
            
            $stmt->execute();
            return true;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }
    
    public function updateUser($id, $dados) {
        try {
            $sql = "UPDATE `{$this->tabela}` SET 
                    primeiro_nome = :primeiro_nome,
                    segundo_nome = :segundo_nome,
                    email = :email,
                    telefone = :telefone,
                    endereco = :endereco,
                    data_atualizacao = NOW()
                    WHERE id_usuario = :id";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(":primeiro_nome", $dados['primeiro_nome'], PDO::PARAM_STR);
            $stmt->bindParam(":segundo_nome", $dados['segundo_nome'], PDO::PARAM_STR);
            $stmt->bindParam(":email", $dados['email'], PDO::PARAM_STR);
            $stmt->bindParam(":telefone", $dados['telefone'], PDO::PARAM_STR);
            $stmt->bindParam(":endereco", $dados['endereco'], PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            
            $stmt->execute();
            return true;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar usuário: " . $e->getMessage());
        }
    }
}



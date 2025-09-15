<?php 

require_once "../controllers/Funcionario.php";

class Crud_funcionario extends Funcionario {
 
    // CRUD CRIAR (CREATE)

    public function create(){
        $nome1 = $this->getNome1();
        $nome2 = $this->getNome2();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $acesso = $this->getAcesso();
        $senha = $this->getSenha();
        $imagem_perfil = $this->getImagem_perfil();
        $sql = "INSERT INTO `{$this->tabela}` (primeiro_nome, segundo_nome, email, telefone, acesso, senha, imagem_perfil) VALUES (:primeiro_nome, :segundo_nome, :email, :telefone, :acesso, :senha, :imagem_perfil)";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":primeiro_nome", $nome1, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $nome2, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":acesso", $acesso, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
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
        $id = $this->getId_funcionario();
        $nome1 = $this->getNome1();
        $nome2 = $this->getNome2();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $acesso = $this->getAcesso();
        $senha = $this->getSenha();
        $imagem_perfil = $this->getImagem_perfil();
        $sql = "UPDATE `{$this->tabela}` SET primeiro_nome = :primeiro_nome, segundo_nome = :segundo_nome, email = :email, telefone = :telefone, acesso = :acesso, senha = :senha, imagem_perfil = :imagem_perfil WHERE id_funcionario = :id";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $conexao = $database->getInstance();
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);
        $stmt->bindParam(":primeiro_nome", $nome1, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $nome2, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":acesso", $acesso, PDO::PARAM_STR);
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

    // CRUD DELETAR (DELETE)
    public function delete(){
        $id = $this->getId_funcionario();
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_funcionario = :id";
        
        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $conexao = $database->getInstance();
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);

        try {
            $stmt->execute();
            echo "<script>alert('Conta deletada com sucesso!'); window.location.href = '../home/index.php';</script>";
            exit();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Método para contar funcionários ativos
    public function countActive() {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` WHERE acesso IN ('Superadmin', 'Admin', 'Entregador', 'Esperante')";
            
            $database = new Database();
            $conexao = $database->getInstance();
            $stmt = $conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar funcionários ativos: " . $e->getMessage());
        }
    }
    
    // Método para contar funcionários por nível de acesso
    public function countByAccess($acesso) {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` WHERE acesso = :acesso";
            
            $database = new Database();
            $conexao = $database->getInstance();
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':acesso', $acesso, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar funcionários por acesso: " . $e->getMessage());
        }
    }
    
    // Método para ler todos os funcionários com paginação
    public function readAll($search = '', $acesso_filter = '', $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT * FROM `{$this->tabela}`";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(primeiro_nome LIKE :search OR segundo_nome LIKE :search OR email LIKE :search OR telefone LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($acesso_filter)) {
                $whereConditions[] = "acesso = :acesso_filter";
                $params['acesso_filter'] = $acesso_filter;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY data_criacao DESC LIMIT :offset, :perPage";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar funcionários: " . $e->getMessage());
        }
    }
    
    // Método count com filtros para paginação
    public function count($search = '', $acesso_filter = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}`";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(primeiro_nome LIKE :search OR segundo_nome LIKE :search OR email LIKE :search OR telefone LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($acesso_filter)) {
                $whereConditions[] = "acesso = :acesso_filter";
                $params['acesso_filter'] = $acesso_filter;
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
            throw new Exception("Erro ao contar funcionários: " . $e->getMessage());
        }
    }
    
    // Método para verificar se email já existe
    public function emailExists($email, $exclude_id = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` WHERE email = :email";
            $params = ['email' => $email];
            
            if ($exclude_id) {
                $sql .= " AND id_funcionario != :exclude_id";
                $params['exclude_id'] = $exclude_id;
            }
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar email: " . $e->getMessage());
        }
    }
    
    // Método para criar funcionário pelo admin
    public function createAdmin() {
        $nome1 = $this->getNome1();
        $nome2 = $this->getNome2();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $acesso = $this->getAcesso();
        $senha = $this->getSenha();
        $imagem_perfil = $this->getImagem_perfil();
        
        $sql = "INSERT INTO `{$this->tabela}` (primeiro_nome, segundo_nome, email, telefone, acesso, senha, imagem_perfil) 
                VALUES (:primeiro_nome, :segundo_nome, :email, :telefone, :acesso, :senha, :imagem_perfil)";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":primeiro_nome", $nome1, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $nome2, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":acesso", $acesso, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);
 
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Método para buscar funcionário por ID
    public function readById($id) {
        try {
            $sql = "SELECT * FROM `{$this->tabela}` WHERE id_funcionario = :id";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar funcionário: " . $e->getMessage());
        }
    }
    
    // Método para atualizar funcionário
    public function updateById($id) {
        $nome1 = $this->getNome1();
        $nome2 = $this->getNome2();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $acesso = $this->getAcesso();
        $senha = $this->getSenha();
        $imagem_perfil = $this->getImagem_perfil();
        
        $sql = "UPDATE `{$this->tabela}` SET 
                primeiro_nome = :primeiro_nome, 
                segundo_nome = :segundo_nome, 
                email = :email, 
                telefone = :telefone, 
                acesso = :acesso";
        
        $params = [
            'primeiro_nome' => $nome1,
            'segundo_nome' => $nome2,
            'email' => $email,
            'telefone' => $telefone,
            'acesso' => $acesso,
            'id' => $id
        ];
        
        // Só atualizar senha se foi fornecida
        if (!empty($senha)) {
            $sql .= ", senha = :senha";
            $params['senha'] = $senha;
        }
        
        // Só atualizar imagem se foi fornecida
        if (!empty($imagem_perfil)) {
            $sql .= ", imagem_perfil = :imagem_perfil";
            $params['imagem_perfil'] = $imagem_perfil;
        }
        
        $sql .= " WHERE id_funcionario = :id";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
 
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Método para excluir funcionário
    public function deleteById($id) {
        try {
            $sql = "DELETE FROM `{$this->tabela}` WHERE id_funcionario = :id";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao excluir funcionário: " . $e->getMessage());
        }
    }
    
    // Método para upload de imagem
    public function uploadImagem($file) {
        // Verificar se o arquivo foi enviado
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erro no upload da imagem');
        }
        
        // Verificar tamanho do arquivo (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('Arquivo muito grande. Máximo 5MB');
        }
        
        // Verificar tipo do arquivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP');
        }
        
        // Gerar nome único para o arquivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('func_') . '.' . $extension;
        
        // Definir diretório de upload
        $uploadDir = __DIR__ . '/../../images/funcionarios/';
        
        // Criar diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . $fileName;
        
        // Mover arquivo para o diretório
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $fileName;
        } else {
            throw new Exception('Erro ao salvar a imagem');
        }
    }
    
}
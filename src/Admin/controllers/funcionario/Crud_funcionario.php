<?php
require_once __DIR__ . '/Funcionario.php';

class Crud_funcionario extends Funcionario {

    // LOGIN - Autenticação
    public function login($email, $senha) {
        $sql = "SELECT * FROM `{$this->tabela}` WHERE email = :email AND senha = :senha";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Preencher objeto com dados do funcionário
                $this->setId_funcionario($result['id_funcionario']);
                $this->setPrimeiro_nome($result['primeiro_nome']);
                $this->setSegundo_nome($result['segundo_nome']);
                $this->setEmail($result['email']);
                $this->setTelefone($result['telefone']);
                $this->setAcesso($result['acesso']);
                $this->setImagem_perfil($result['imagem_perfil']);
                
                return true;
            }
            return false;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao realizar login: " . $e->getMessage());
        }
    }

    // READ ALL - Listar todos os funcionários
    public function readAll($search = '', $acesso_filter = '', $limit = 10, $offset = 0) {
        $where_clauses = [];
        $params = [];
        
        if (!empty($search)) {
            $where_clauses[] = "(primeiro_nome LIKE :search1 OR segundo_nome LIKE :search2 OR email LIKE :search3)";
            $params[':search1'] = "%$search%";
            $params[':search2'] = "%$search%";
            $params[':search3'] = "%$search%";
        }
        
        if (!empty($acesso_filter)) {
            $where_clauses[] = "acesso = :acesso";
            $params[':acesso'] = $acesso_filter;
        }
        
        $where_clause = '';
        if (!empty($where_clauses)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_clauses);
        }
        
        $sql = "SELECT * FROM `{$this->tabela}` $where_clause ORDER BY data_criacao DESC LIMIT :limit OFFSET :offset";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar funcionários: " . $e->getMessage());
        }
    }

    // COUNT - Contar total de registros
    public function count($search = '', $acesso_filter = '') {
        $where_clauses = [];
        $params = [];
        
        if (!empty($search)) {
            $where_clauses[] = "(primeiro_nome LIKE :search1 OR segundo_nome LIKE :search2 OR email LIKE :search3)";
            $params[':search1'] = "%$search%";
            $params[':search2'] = "%$search%";
            $params[':search3'] = "%$search%";
        }
        
        if (!empty($acesso_filter)) {
            $where_clauses[] = "acesso = :acesso";
            $params[':acesso'] = $acesso_filter;
        }
        
        $where_clause = '';
        if (!empty($where_clauses)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_clauses);
        }
        
        $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` $where_clause";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar funcionários: " . $e->getMessage());
        }
    }

    // READ BY ID - Buscar por ID
    public function readById($id) {
        $sql = "SELECT * FROM `{$this->tabela}` WHERE id_funcionario = :id";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar funcionário: " . $e->getMessage());
        }
    }

    // CREATE - Criar funcionário
    public function create() {
        $primeiro_nome = $this->getPrimeiro_nome();
        $segundo_nome = $this->getSegundo_nome();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $acesso = $this->getAcesso();
        $senha = $this->getSenha();
        $imagem_perfil = $this->getImagem_perfil();
        
        // Verificar se email já existe
        $sql_check = "SELECT COUNT(*) FROM `{$this->tabela}` WHERE email = :email";
        $database = new Database();
        $stmt_check = $database->prepare($sql_check);
        $stmt_check->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt_check->execute();
        
        if ($stmt_check->fetchColumn() > 0) {
            throw new Exception("Este e-mail já está sendo usado.");
        }
        
        $sql = "INSERT INTO `{$this->tabela}` (primeiro_nome, segundo_nome, email, telefone, acesso, senha, imagem_perfil) 
                VALUES (:primeiro_nome, :segundo_nome, :email, :telefone, :acesso, :senha, :imagem_perfil)";
        
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":primeiro_nome", $primeiro_nome, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $segundo_nome, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":acesso", $acesso, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);
        
        try {
            $stmt->execute();
            return $database->getInstance()->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar funcionário: " . $e->getMessage());
        }
    }

    // UPDATE - Atualizar funcionário por ID
    public function update($id) {
        $primeiro_nome = $this->getPrimeiro_nome();
        $segundo_nome = $this->getSegundo_nome();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $acesso = $this->getAcesso();
        $imagem_perfil = $this->getImagem_perfil();
        $senha = $this->getSenha();
        
        // Verificar se email já existe (exceto para o usuário atual)
        $sql_check = "SELECT COUNT(*) FROM `{$this->tabela}` WHERE email = :email AND id_funcionario != :id";
        $database = new Database();
        $stmt_check = $database->prepare($sql_check);
        $stmt_check->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt_check->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt_check->execute();
        
        if ($stmt_check->fetchColumn() > 0) {
            throw new Exception("Este e-mail já está sendo usado por outro funcionário.");
        }
        
        $sql = "UPDATE `{$this->tabela}` SET 
                primeiro_nome = :primeiro_nome,
                segundo_nome = :segundo_nome,
                email = :email,
                telefone = :telefone,
                acesso = :acesso,
                imagem_perfil = :imagem_perfil";
        
        // Adicionar senha na query se foi fornecida
        if (!empty($senha)) {
            $sql .= ", senha = :senha";
        }
        
        $sql .= " WHERE id_funcionario = :id";
        
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":primeiro_nome", $primeiro_nome, PDO::PARAM_STR);
        $stmt->bindParam(":segundo_nome", $segundo_nome, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":acesso", $acesso, PDO::PARAM_STR);
        $stmt->bindParam(":imagem_perfil", $imagem_perfil, PDO::PARAM_STR);
        
        if (!empty($senha)) {
            $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        }
        
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar funcionário: " . $e->getMessage());
        }
    }

    // DELETE - Excluir funcionário
    public function delete($id) {
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_funcionario = :id";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Erro ao excluir funcionário: " . $e->getMessage());
        }
    }
    
    // Upload de imagem de perfil
    public function uploadImagem($file) {
        $uploadDir = __DIR__ . "/../../../images/funcionarios/";
        
        // Criar diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erro no upload da imagem.");
        }
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Tipo de arquivo não permitido. Use JPEG, PNG ou GIF.");
        }
        
        if ($file['size'] > $maxSize) {
            throw new Exception("Arquivo muito grande. Máximo 2MB.");
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'funcionario_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Erro ao salvar imagem.");
        }
        
        return $filename;
    }
    
    // Contar funcionários por acesso
    public function countByStatus($acesso = null) {
        try {
            if ($acesso === null) {
                // Contar todos os funcionários
                $sql = "SELECT COUNT(*) as total FROM funcionario";
                $stmt = $this->prepare($sql);
            } else {
                // Contar por nível de acesso
                $sql = "SELECT COUNT(*) as total FROM funcionario WHERE acesso = :acesso";
                $stmt = $this->prepare($sql);
                $stmt->bindValue(':acesso', $acesso, PDO::PARAM_STR);
            }
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar funcionários por status: " . $e->getMessage());
        }
    }
    
    // Contar funcionários ativos (todos exceto inativos - para compatibilidade)
    public function countActive() {
        try {
            $sql = "SELECT COUNT(*) as total FROM funcionario WHERE acesso IN ('Superadmin', 'Admin', 'Entregador', 'Esperante')";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar funcionários ativos: " . $e->getMessage());
        }
    }
    
    // Alterar nível de acesso do funcionário
    public function updateAccess($id, $acesso) {
        try {
            $sql = "UPDATE funcionario SET acesso = :acesso WHERE id_funcionario = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':acesso', $acesso, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao alterar acesso do funcionário: " . $e->getMessage());
        }
    }
}
?>

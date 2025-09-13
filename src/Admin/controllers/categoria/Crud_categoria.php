<?php
require_once "Categoria.php";

class Crud_categoria extends Categoria {
    
    // Obter todas as categorias com busca e paginação
    public function readAll($search = '', $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT * FROM categoria";
            $params = [];
            
            if (!empty($search)) {
                $sql .= " WHERE nome_categoria LIKE :search";
                $params['search'] = "%$search%";
            }
            
            $sql .= " ORDER BY id_categoria DESC LIMIT :offset, :perPage";
            
            $stmt = $this->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar categorias: " . $e->getMessage());
        }
    }
    
    // Contar total de categorias com busca
    public function count($search = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM categoria";
            $params = [];
            
            if (!empty($search)) {
                $sql .= " WHERE nome_categoria LIKE :search";
                $params['search'] = "%$search%";
            }
            
            $stmt = $this->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar categorias: " . $e->getMessage());
        }
    }
    
    // Buscar categoria por ID
    public function readById($id) {
        try {
            $sql = "SELECT * FROM categoria WHERE id_categoria = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar categoria: " . $e->getMessage());
        }
    }
    
    // Criar nova categoria
    public function create() {
        try {
            // Verificar se já existe
            $sql = "SELECT COUNT(*) FROM categoria WHERE nome_categoria = :nome_categoria";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nome_categoria', $this->getNomeCategoria());
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Esta categoria já existe.");
            }
            
            $sql = "INSERT INTO categoria (nome_categoria) VALUES (:nome_categoria)";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nome_categoria', $this->getNomeCategoria());
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar categoria: " . $e->getMessage());
        }
    }
    
    // Atualizar categoria
    public function update($id) {
        try {
            // Verificar se já existe (exceto a categoria atual)
            $sql = "SELECT COUNT(*) FROM categoria WHERE nome_categoria = :nome_categoria AND id_categoria != :id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nome_categoria', $this->getNomeCategoria());
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Esta categoria já existe.");
            }
            
            $sql = "UPDATE categoria SET nome_categoria = :nome_categoria WHERE id_categoria = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nome_categoria', $this->getNomeCategoria());
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar categoria: " . $e->getMessage());
        }
    }
    
    // Deletar categoria
    public function delete($id) {
        try {
            // Verificar se há produtos vinculados
            $sql = "SELECT COUNT(*) FROM produto WHERE id_categoria = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Não é possível excluir esta categoria pois há produtos vinculados a ela.");
            }
            
            $sql = "DELETE FROM categoria WHERE id_categoria = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao deletar categoria: " . $e->getMessage());
        }
    }
    
    // Obter todas as categorias sem paginação (para selects)
    public function getAll() {
        try {
            $sql = "SELECT * FROM categoria ORDER BY nome_categoria";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar categorias: " . $e->getMessage());
        }
    }
    
    // Verificar se nome da categoria já existe
    public function nomeExists($nome, $excludeId = null) {
        try {
            $sql = "SELECT id_categoria FROM categoria WHERE nome_categoria = :nome";
            $params = [':nome' => $nome];
            
            if ($excludeId) {
                $sql .= " AND id_categoria != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            
            $stmt = $this->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar nome da categoria: " . $e->getMessage());
        }
    }
    
    // Obter todas as categorias para select
    public function readAllForSelect() {
        try {
            $sql = "SELECT id_categoria, nome_categoria FROM categoria ORDER BY nome_categoria ASC";
            
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar categorias: " . $e->getMessage());
        }
    }
}
?>

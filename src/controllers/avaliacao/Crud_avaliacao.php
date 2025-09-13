<?php 

require_once __DIR__ . "/Avaliacao.php";

class Crud_avaliacao extends Avaliacao {
    
    private $conexao;
    
    public function __construct($conexao = null) {
        if ($conexao) {
            $this->conexao = $conexao;
        } else {
            // Se não recebeu conexão, criar uma nova
            require_once __DIR__ . "/../../db/conection.php";
            $database = new Database();
            $this->conexao = $database->getInstance();
        }
    }
    
    // CRUD CRIAR (CREATE)
    public function create() {
        $nota = $this->getNota();
        $texto_avaliacao = $this->getTexto_avaliacao();
        $status = $this->getStatus();
        $resposta = $this->getResposta();
        $id_pedido = $this->getId_pedido();
        $id_usuario = $this->getId_usuario();
        
        $sql = "INSERT INTO `{$this->tabela}` (nota, texto_avaliacao, data_avaliacao, status, resposta, id_pedido, id_usuario) 
                VALUES (:nota, :texto_avaliacao, NOW(), :status, :resposta, :id_pedido, :id_usuario)";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":nota", $nota, PDO::PARAM_INT);
        $stmt->bindParam(":texto_avaliacao", $texto_avaliacao, PDO::PARAM_STR);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":resposta", $resposta, PDO::PARAM_STR);
        $stmt->bindParam(":id_pedido", $id_pedido, PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return $this->conexao->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // CRUD LER (READ)
    public function read($id) {
        $sql = "SELECT av.*, u.primeiro_nome, u.segundo_nome, u.email, p.id_pedido
                FROM `{$this->tabela}` av 
                LEFT JOIN usuario u ON av.id_usuario = u.id_usuario
                LEFT JOIN pedido p ON av.id_pedido = p.id_pedido
                WHERE av.id_avaliacao = :id";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // CRUD LER TODOS (READ ALL)
    public function readAll($search = '', $status = '', $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT av.*, u.primeiro_nome, u.segundo_nome, u.email, p.id_pedido
                    FROM `{$this->tabela}` av 
                    LEFT JOIN usuario u ON av.id_usuario = u.id_usuario
                    LEFT JOIN pedido p ON av.id_pedido = p.id_pedido";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.primeiro_nome LIKE :search OR u.segundo_nome LIKE :search OR u.email LIKE :search OR av.texto_avaliacao LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $whereConditions[] = "av.status = :status";
                $params['status'] = $status;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY av.data_avaliacao DESC LIMIT :offset, :perPage";
            
            $stmt = $this->conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar avaliações: " . $e->getMessage());
        }
    }
    
    // CRUD ATUALIZAR (UPDATE)
    public function update($id) {
        $nota = $this->getNota();
        $texto_avaliacao = $this->getTexto_avaliacao();
        $status = $this->getStatus();
        $resposta = $this->getResposta();
        
        $sql = "UPDATE `{$this->tabela}` SET 
                nota = :nota,
                texto_avaliacao = :texto_avaliacao,
                status = :status,
                resposta = :resposta
                WHERE id_avaliacao = :id";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":nota", $nota, PDO::PARAM_INT);
        $stmt->bindParam(":texto_avaliacao", $texto_avaliacao, PDO::PARAM_STR);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":resposta", $resposta, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // CRUD DELETAR (DELETE)
    public function delete($id) {
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_avaliacao = :id";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Método para contar avaliações por status
    public function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` WHERE status = :status";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar avaliações: " . $e->getMessage());
        }
    }
    
    // Método para contar todas as avaliações com filtros opcionais
    public function count($search = '', $status = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` av 
                    LEFT JOIN usuario u ON av.id_usuario = u.id_usuario";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.primeiro_nome LIKE :search OR u.segundo_nome LIKE :search OR u.email LIKE :search OR av.texto_avaliacao LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $whereConditions[] = "av.status = :status";
                $params['status'] = $status;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $stmt = $this->conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar avaliações: " . $e->getMessage());
        }
    }
    
    // Método para contar avaliações de hoje
    public function countToday() {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` WHERE DATE(data_avaliacao) = CURDATE()";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar avaliações de hoje: " . $e->getMessage());
        }
    }
    
    // Método para calcular média de avaliações
    public function getMediaAvaliacoes() {
        try {
            $sql = "SELECT AVG(nota) as media FROM `{$this->tabela}` WHERE status = 'Aprovada'";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return round($result['media'] ?? 0, 1);
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular média: " . $e->getMessage());
        }
    }
    
    // Método para responder uma avaliação
    public function responder($id, $resposta) {
        $sql = "UPDATE `{$this->tabela}` SET 
                resposta = :resposta,
                status = 'Respondida'
                WHERE id_avaliacao = :id";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":resposta", $resposta, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao responder avaliação: " . $e->getMessage());
        }
    }
}
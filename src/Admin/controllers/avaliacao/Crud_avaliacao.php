<?php
require_once "Avaliacao.php";

class Crud_avaliacao extends Avaliacao {
    
    // Obter todas as avaliações com busca e paginação
    public function readAll($search = '', $status = '', $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT a.*, u.primeiro_nome, u.segundo_nome, u.email
                    FROM avaliacao a 
                    LEFT JOIN usuario u ON a.id_usuario = u.id_usuario";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.primeiro_nome LIKE :search OR u.segundo_nome LIKE :search OR a.texto_avaliacao LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $whereConditions[] = "a.status = :status";
                $params['status'] = $status;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY a.data_avaliacao DESC LIMIT :offset, :perPage";
            
            $stmt = $this->prepare($sql);
            
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
    
    // Contar total de avaliações com busca
    public function count($search = '', $status = '') {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM avaliacao a 
                    LEFT JOIN usuario u ON a.id_usuario = u.id_usuario";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.primeiro_nome LIKE :search OR u.segundo_nome LIKE :search OR a.texto_avaliacao LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $whereConditions[] = "a.status = :status";
                $params['status'] = $status;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $stmt = $this->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar avaliações: " . $e->getMessage());
        }
    }
    
    // Buscar avaliação por ID
    public function readById($id) {
        try {
            $sql = "SELECT a.*, u.primeiro_nome, u.segundo_nome, u.email
                    FROM avaliacao a 
                    LEFT JOIN usuario u ON a.id_usuario = u.id_usuario 
                    WHERE a.id_avaliacao = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar avaliação: " . $e->getMessage());
        }
    }
    
    // Atualizar status da avaliação
    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE avaliacao SET status = :status WHERE id_avaliacao = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar status da avaliação: " . $e->getMessage());
        }
    }
    
    // Deletar avaliação
    public function delete($id) {
        try {
            $sql = "DELETE FROM avaliacao WHERE id_avaliacao = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao deletar avaliação: " . $e->getMessage());
        }
    }
    
    // Aprovar avaliação
    public function aprovar($id) {
        return $this->updateStatus($id, 'aprovada');
    }
    
    // Reprovar avaliação
    public function reprovar($id) {
        return $this->updateStatus($id, 'reprovada');
    }
    
    // Obter estatísticas das avaliações
    public function getEstatisticas() {
        try {
            $stats = [];
            
            // Média geral das notas
            $sql = "SELECT AVG(nota) as media_geral FROM avaliacao WHERE status = 'aprovada'";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $stats['media_geral'] = round($stmt->fetch(PDO::FETCH_ASSOC)['media_geral'], 2);
            
            // Total por status
            $sql = "SELECT status, COUNT(*) as total FROM avaliacao GROUP BY status";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $statusStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($statusStats as $stat) {
                $stats['status'][$stat['status']] = $stat['total'];
            }
            
            // Distribuição das notas
            $sql = "SELECT nota, COUNT(*) as total FROM avaliacao WHERE status = 'aprovada' GROUP BY nota ORDER BY nota";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $notasStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($notasStats as $stat) {
                $stats['notas'][$stat['nota']] = $stat['total'];
            }
            
            // Avaliações recentes (últimos 7 dias)
            $sql = "SELECT COUNT(*) as total FROM avaliacao WHERE data_avaliacao >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $stats['ultimos_7_dias'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return $stats;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter estatísticas: " . $e->getMessage());
        }
    }
    
    // Obter status disponíveis
    public function getStatusDisponiveis() {
        return [
            'pendente' => 'Pendente',
            'aprovada' => 'Aprovada',
            'reprovada' => 'Reprovada'
        ];
    }
    
    // Obter avaliações pendentes de moderação
    public function getPendentes($limit = 5) {
        try {
            $sql = "SELECT a.*, u.primeiro_nome, u.segundo_nome
                    FROM avaliacao a 
                    LEFT JOIN usuario u ON a.id_usuario = u.id_usuario 
                    WHERE a.status = 'pendente'
                    ORDER BY a.data_avaliacao DESC
                    LIMIT :limit";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar avaliações pendentes: " . $e->getMessage());
        }
    }
}
?>

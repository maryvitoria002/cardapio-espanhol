<?php
require_once "Pedido.php";

class Crud_pedido extends Pedido {
    
    // Obter todos os pedidos com busca e paginação
    public function readAll($search = '', $status = '', $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT p.*, u.primeiro_nome, u.segundo_nome, u.email,
                           SUM(pp.quantidade * pp.preco) as total_pedido
                    FROM pedido p 
                    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
                    LEFT JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.primeiro_nome LIKE :search OR u.segundo_nome LIKE :search OR u.email LIKE :search OR p.id_pedido LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $whereConditions[] = "p.status_pedido = :status";
                $params['status'] = $status;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " GROUP BY p.id_pedido ORDER BY p.data_pedido DESC LIMIT :offset, :perPage";
            
            $stmt = $this->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar pedidos: " . $e->getMessage());
        }
    }
    
    // Contar total de pedidos com busca
    public function count($search = '', $status = '') {
        try {
            $sql = "SELECT COUNT(DISTINCT p.id_pedido) as total 
                    FROM pedido p 
                    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(u.primeiro_nome LIKE :search OR u.segundo_nome LIKE :search OR u.email LIKE :search OR p.id_pedido LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $whereConditions[] = "p.status_pedido = :status";
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
            throw new Exception("Erro ao contar pedidos: " . $e->getMessage());
        }
    }
    
    // Buscar pedido por ID
    public function readById($id) {
        try {
            $sql = "SELECT p.*, u.primeiro_nome, u.segundo_nome, u.email, u.telefone
                    FROM pedido p 
                    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario 
                    WHERE p.id_pedido = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar pedido: " . $e->getMessage());
        }
    }
    
    // Buscar itens do pedido
    public function getItensPedido($idPedido) {
        try {
            $sql = "SELECT pp.*, pr.nome_produto, pr.imagem
                    FROM produto_pedido pp
                    LEFT JOIN produto pr ON pp.id_produto = pr.id_produto
                    WHERE pp.id_pedido = :id_pedido";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar itens do pedido: " . $e->getMessage());
        }
    }
    
    // Atualizar status do pedido
    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE pedido SET status_pedido = :status WHERE id_pedido = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar status do pedido: " . $e->getMessage());
        }
    }
    
    // Atualizar dados de entrega
    public function updateEntrega($id, $dataEntrega, $endereco) {
        try {
            $sql = "UPDATE pedido SET data_entrega = :data_entrega, endereco = :endereco WHERE id_pedido = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':data_entrega', $dataEntrega);
            $stmt->bindValue(':endereco', $endereco);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar dados de entrega: " . $e->getMessage());
        }
    }
    
    // Deletar pedido
    public function delete($id) {
        try {
            // Primeiro, deletar os itens do pedido
            $sql = "DELETE FROM produto_pedido WHERE id_pedido = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Depois, deletar o pedido
            $sql = "DELETE FROM pedido WHERE id_pedido = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao deletar pedido: " . $e->getMessage());
        }
    }
    
    // Obter estatísticas dos pedidos
    public function getEstatisticas() {
        try {
            $stats = [];
            
            // Total de pedidos por status
            $sql = "SELECT status_pedido, COUNT(*) as total FROM pedido GROUP BY status_pedido";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $statusStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($statusStats as $stat) {
                $stats['status'][$stat['status_pedido']] = $stat['total'];
            }
            
            // Pedidos hoje
            $sql = "SELECT COUNT(*) as total FROM pedido WHERE DATE(data_pedido) = CURDATE()";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $stats['hoje'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Receita total
            $sql = "SELECT SUM(pp.quantidade * pp.preco) as total_receita 
                    FROM produto_pedido pp 
                    JOIN pedido p ON pp.id_pedido = p.id_pedido 
                    WHERE p.status_pedido = 'Concluido'";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $stats['receita_total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_receita'] ?? 0;
            
            return $stats;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter estatísticas: " . $e->getMessage());
        }
    }
    
    // Obter status disponíveis
    public function getStatusDisponiveis() {
        return [
            'Pendente',
            'Confirmado',
            'Preparando',
            'Saiu para entrega',
            'Entregue',
            'Cancelado',
            'Concluido'
        ];
    }
    
    // Contar pedidos de hoje
    public function countToday() {
        try {
            $sql = "SELECT COUNT(*) as total FROM pedido WHERE DATE(data_pedido) = CURDATE()";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar pedidos de hoje: " . $e->getMessage());
        }
    }
    
    // Receita total
    public function getReceitaTotal() {
        try {
            $sql = "SELECT COALESCE(SUM(pp.quantidade * pp.preco), 0) as total
                    FROM pedido p
                    JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                    WHERE p.status_pedido IN ('Entregue', 'Concluido')";
            
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular receita total: " . $e->getMessage());
        }
    }
    
    // Receita do mês
    public function getReceitaMes() {
        try {
            $sql = "SELECT COALESCE(SUM(pp.quantidade * pp.preco), 0) as total
                    FROM pedido p
                    JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                    WHERE p.status_pedido IN ('Entregue', 'Concluido')
                    AND MONTH(p.data_pedido) = MONTH(CURDATE())
                    AND YEAR(p.data_pedido) = YEAR(CURDATE())";
            
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular receita do mês: " . $e->getMessage());
        }
    }
    
    // Receita de hoje
    public function getReceitaHoje() {
        try {
            $sql = "SELECT COALESCE(SUM(pp.quantidade * pp.preco), 0) as total
                    FROM pedido p
                    JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                    WHERE p.status_pedido IN ('Entregue', 'Concluido')
                    AND DATE(p.data_pedido) = CURDATE()";
            
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular receita de hoje: " . $e->getMessage());
        }
    }
    
    // Contar pedidos por status
    public function getCountByStatus() {
        try {
            $sql = "SELECT status_pedido, COUNT(*) as total 
                    FROM pedido 
                    GROUP BY status_pedido";
            
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar pedidos por status: " . $e->getMessage());
        }
    }
}
?>

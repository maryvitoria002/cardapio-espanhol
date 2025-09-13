<?php 

require_once "Pedido.php";

class Crud_pedido extends Pedido {
    
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
    public function create(){
        $id_usuario = $this->getId_usuario();
        $endereco = $this->getEndereco();
        $modo_pagamento = $this->getModo_pagamento();
        $status_pagamento = $this->getStatus_pagamento();
        $status_pedido = $this->getStatus_pedido();
        $motivo_cancelamento = $this->getMotivo_cancelamento();
        $nota = $this->getNota();
        
        $sql = "INSERT INTO `{$this->tabela}` (id_usuario, endereco, modo_pagamento, status_pagamento, data_pedido, status_pedido, motivo_cancelamento, nota) 
                VALUES (:id_usuario, :endereco, :modo_pagamento, :status_pagamento, NOW(), :status_pedido, :motivo_cancelamento, :nota)";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(":endereco", $endereco, PDO::PARAM_STR);
        $stmt->bindParam(":modo_pagamento", $modo_pagamento, PDO::PARAM_STR);
        $stmt->bindParam(":status_pagamento", $status_pagamento, PDO::PARAM_STR);
        $stmt->bindParam(":status_pedido", $status_pedido, PDO::PARAM_STR);
        $stmt->bindParam(":motivo_cancelamento", $motivo_cancelamento, PDO::PARAM_STR);
        $stmt->bindParam(":nota", $nota, PDO::PARAM_STR);
        
        try {
            $stmt->execute();
            $pdo = $database->getInstance();
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // CRUD LER (READ)
    public function read(){
        $sql = "SELECT p.*, u.primeiro_nome, u.segundo_nome, u.email 
                FROM `{$this->tabela}` p
                LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
                ORDER BY p.data_pedido DESC";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // CRUD LER POR ID
    public function readById($id){
        $sql = "SELECT p.*, u.primeiro_nome, u.segundo_nome, u.email 
                FROM `{$this->tabela}` p
                LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
                WHERE p.id_pedido = :id";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Método para ler todos os pedidos (para dashboard)
    public function readAll($search = '', $status = '', $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT p.*, u.primeiro_nome, u.segundo_nome, u.email,
                           COALESCE(SUM(pp.quantidade * pp.preco), 0) as total_pedido
                    FROM `{$this->tabela}` p 
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
            
            $stmt = $this->conexao->prepare($sql);
            
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
    
    // Método para buscar pedidos de um usuário específico
    public function readByUser($id_usuario) {
        try {
            $sql = "SELECT p.*, 
                           COALESCE(SUM(pp.quantidade * pp.preco), 0) as total_pedido,
                           COUNT(pp.id_produto) as total_itens
                    FROM `{$this->tabela}` p 
                    LEFT JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                    WHERE p.id_usuario = :id_usuario
                    GROUP BY p.id_pedido 
                    ORDER BY p.data_pedido DESC";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar itens de cada pedido
            foreach ($pedidos as &$pedido) {
                $pedido['itens'] = $this->getItensPedido($pedido['id_pedido']);
            }
            
            return $pedidos;
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Método para buscar itens de um pedido específico
    public function getItensPedido($id_pedido) {
        try {
            $sql = "SELECT pp.*, prod.nome_produto, prod.imagem
                    FROM produto_pedido pp
                    INNER JOIN produto prod ON pp.id_produto = prod.id_produto
                    WHERE pp.id_pedido = :id_pedido
                    ORDER BY prod.nome_produto";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar itens do pedido: " . $e->getMessage());
        }
    }
    
    // CRUD ATUALIZAR (UPDATE)
    public function update($id){
        $endereco = $this->getEndereco();
        $modo_pagamento = $this->getModo_pagamento();
        $status_pagamento = $this->getStatus_pagamento();
        $status_pedido = $this->getStatus_pedido();
        $motivo_cancelamento = $this->getMotivo_cancelamento();
        $nota = $this->getNota();
        
        $sql = "UPDATE `{$this->tabela}` SET 
                endereco = :endereco,
                modo_pagamento = :modo_pagamento,
                status_pagamento = :status_pagamento,
                status_pedido = :status_pedido,
                motivo_cancelamento = :motivo_cancelamento,
                nota = :nota,
                data_atualizacao = NOW()
                WHERE id_pedido = :id";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":endereco", $endereco, PDO::PARAM_STR);
        $stmt->bindParam(":modo_pagamento", $modo_pagamento, PDO::PARAM_STR);
        $stmt->bindParam(":status_pagamento", $status_pagamento, PDO::PARAM_STR);
        $stmt->bindParam(":status_pedido", $status_pedido, PDO::PARAM_STR);
        $stmt->bindParam(":motivo_cancelamento", $motivo_cancelamento, PDO::PARAM_STR);
        $stmt->bindParam(":nota", $nota, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // CRUD DELETAR (DELETE)
    public function delete($id){
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_pedido = :id";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Método para contar pedidos por status
    public function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` WHERE status_pedido = :status";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar pedidos: " . $e->getMessage());
        }
    }
    
    // Método para contar todos os pedidos com filtros opcionais
    public function count($search = '', $status = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` p 
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
            
            $stmt = $this->conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar pedidos: " . $e->getMessage());
        }
    }
    
    // Método para buscar pedidos recentes
    public function getRecentes($limite = 5) {
        try {
            $sql = "SELECT p.*, u.primeiro_nome, u.segundo_nome 
                    FROM `{$this->tabela}` p
                    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
                    ORDER BY p.data_pedido DESC
                    LIMIT :limite";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar pedidos recentes: " . $e->getMessage());
        }
    }
    
    // Método para calcular faturamento total
    public function getFaturamentoTotal() {
        try {
            $sql = "SELECT SUM(pp.quantidade * pp.preco) as total 
                    FROM `{$this->tabela}` p
                    INNER JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                    WHERE p.status_pedido = 'concluido'";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular faturamento: " . $e->getMessage());
        }
    }
    
    // Método para buscar pedidos de hoje
    public function countHoje() {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` 
                    WHERE DATE(data_pedido) = CURDATE()";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar pedidos de hoje: " . $e->getMessage());
        }
    }
    
    // Alias para compatibilidade com o dashboard
    public function countToday() {
        return $this->countHoje();
    }
    
    // Receita total
    public function getReceitaTotal() {
        try {
            $sql = "SELECT COALESCE(SUM(pp.quantidade * pp.preco), 0) as total
                    FROM `{$this->tabela}` p
                    JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                    WHERE p.status_pedido IN ('Entregue', 'Concluido')";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular receita total: " . $e->getMessage());
        }
    }
    
    // Receita do mês
    public function getReceitaMes() {
        try {
            $sql = "SELECT COALESCE(SUM(pp.quantidade * pp.preco), 0) as total
                    FROM `{$this->tabela}` p
                    JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                    WHERE p.status_pedido IN ('Entregue', 'Concluido')
                    AND MONTH(p.data_pedido) = MONTH(CURDATE())
                    AND YEAR(p.data_pedido) = YEAR(CURDATE())";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular receita do mês: " . $e->getMessage());
        }
    }
    
    // Receita de hoje
    public function getReceitaHoje() {
        try {
            $sql = "SELECT COALESCE(SUM(pp.quantidade * pp.preco), 0) as total
                    FROM `{$this->tabela}` p
                    JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                    WHERE p.status_pedido IN ('Entregue', 'Concluido')
                    AND DATE(p.data_pedido) = CURDATE()";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular receita de hoje: " . $e->getMessage());
        }
    }
    
    // Contar pedidos por status
    public function getCountByStatus() {
        try {
            $sql = "SELECT status_pedido, COUNT(*) as total 
                    FROM `{$this->tabela}` 
                    GROUP BY status_pedido";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar pedidos por status: " . $e->getMessage());
        }
    }
    
    // Método para atualizar status do pedido
    public function updateStatus($id, $status, $observacoes = '') {
        try {
            $sql = "UPDATE `{$this->tabela}` SET 
                    status_pedido = :status";
            
            if (!empty($observacoes)) {
                $sql .= ", motivo_cancelamento = :observacoes";
            }
            
            $sql .= " WHERE id_pedido = :id";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if (!empty($observacoes)) {
                $stmt->bindParam(':observacoes', $observacoes, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar status: " . $e->getMessage());
        }
    }
    
    // Método para deletar por ID (alias para delete)
    public function deleteById($id) {
        return $this->delete($id);
    }
    
    // Método para calcular total do pedido
    public function getTotalPedido($idPedido) {
        try {
            $sql = "SELECT COALESCE(SUM(pp.quantidade * pp.preco), 0) as total
                    FROM produto_pedido pp
                    WHERE pp.id_pedido = :id_pedido";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular total do pedido: " . $e->getMessage());
        }
    }
}
?>
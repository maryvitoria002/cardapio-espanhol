<?php 

require_once "Produto.php";

class Crud_produto extends Produto {
 
    // CRUD CRIAR (CREATE)
    public function create(){
        $nome_produto = $this->getNome_produto();
        $preco = $this->getPreco();
        $estoque = $this->getEstoque();
        $status = $this->getStatus();
        $descricao = $this->getDescricao();
        $imagem = $this->getImagem();
        
        $sql = "INSERT INTO `{$this->tabela}` (nome_produto, preco, estoque, status, descricao, imagem, data_criacao) 
                VALUES (:nome_produto, :preco, :estoque, :status, :descricao, :imagem, NOW())";

        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":nome_produto", $nome_produto, PDO::PARAM_STR);
        $stmt->bindParam(":preco", $preco, PDO::PARAM_STR);
        $stmt->bindParam(":estoque", $estoque, PDO::PARAM_INT);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":descricao", $descricao, PDO::PARAM_STR);
        $stmt->bindParam(":imagem", $imagem, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // CRUD LER (READ)
    public function read(){
        $id_produto = $this->getId_produto();
        
        if ($id_produto) {
            // Se há ID definido, buscar produto específico
            $sql = "SELECT * FROM `{$this->tabela}` WHERE id_produto = :id";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(":id", $id_produto, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Se não há ID, buscar todos os produtos
            $sql = "SELECT * FROM `{$this->tabela}` ORDER BY data_criacao DESC";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    // CRUD LER POR ID
    public function readById($id){
        $sql = "SELECT * FROM `{$this->tabela}` WHERE id_produto = :id";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // CRUD ATUALIZAR (UPDATE)
    public function update($id){
        $nome_produto = $this->getNome_produto();
        $preco = $this->getPreco();
        $estoque = $this->getEstoque();
        $status = $this->getStatus();
        $descricao = $this->getDescricao();
        $imagem = $this->getImagem();
        
        $sql = "UPDATE `{$this->tabela}` SET 
                nome_produto = :nome_produto,
                preco = :preco,
                estoque = :estoque,
                status = :status,
                descricao = :descricao,
                imagem = :imagem,
                data_atualizacao = NOW()
                WHERE id_produto = :id";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":nome_produto", $nome_produto, PDO::PARAM_STR);
        $stmt->bindParam(":preco", $preco, PDO::PARAM_STR);
        $stmt->bindParam(":estoque", $estoque, PDO::PARAM_INT);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":descricao", $descricao, PDO::PARAM_STR);
        $stmt->bindParam(":imagem", $imagem, PDO::PARAM_STR);
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
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_produto = :id";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Método para buscar produtos mais vendidos
    public function getMaisVendidos($limite = 5) {
        try {
            $sql = "SELECT p.*, SUM(pp.quantidade) as total_vendido 
                    FROM produto p
                    INNER JOIN produto_pedido pp ON p.id_produto = pp.id_produto
                    INNER JOIN pedido ped ON pp.id_pedido = ped.id_pedido
                    WHERE ped.status_pedido = 'concluido'
                    GROUP BY p.id_produto
                    ORDER BY total_vendido DESC
                    LIMIT :limite";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos mais vendidos: " . $e->getMessage());
        }
    }
    
    // Método para contar todos os produtos
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}`";
            
            $database = new Database();
            $conexao = $database->getInstance();
            $stmt = $conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar produtos: " . $e->getMessage());
        }
    }

    // Método para contar produtos por status
    public function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` WHERE status = :status";
            
            $database = new Database();
            $conexao = $database->getInstance();
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar produtos: " . $e->getMessage());
        }
    }
    
    // Método para buscar produtos com baixo estoque
    public function getBaixoEstoque($limite = 10) {
        try {
            $sql = "SELECT * FROM `{$this->tabela}` 
                    WHERE estoque <= :limite AND status = 'ativo'
                    ORDER BY estoque ASC";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos com baixo estoque: " . $e->getMessage());
        }
    }
    
    // Método para buscar produtos por categoria (excluindo um produto específico)
    public function readByCategoria($id_categoria, $limite = 4, $excluir_id = null) {
        try {
            $sql = "SELECT * FROM `{$this->tabela}` 
                    WHERE id_categoria = :id_categoria 
                    AND status = 'Disponivel'";
            
            if ($excluir_id) {
                $sql .= " AND id_produto != :excluir_id";
            }
            
            $sql .= " ORDER BY RAND() LIMIT :limite";
            
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            
            if ($excluir_id) {
                $stmt->bindParam(':excluir_id', $excluir_id, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos relacionados: " . $e->getMessage());
        }
    }
}
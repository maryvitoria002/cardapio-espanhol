<?php 

require_once "../controllers/Produto_pedido.php";

class Crud_produto_pedido extends Produto_pedido {
    
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
        $id_pedido = $this->getId_pedido();
        $id_produto = $this->getId_produto();
        $quantidade = $this->getQuantidade();
        $preco = $this->getPreco();
        
        $sql = "INSERT INTO `{$this->tabela}` (id_pedido, id_produto, quantidade, preco) 
                VALUES (:id_pedido, :id_produto, :quantidade, :preco)";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_pedido", $id_pedido, PDO::PARAM_INT);
        $stmt->bindParam(":id_produto", $id_produto, PDO::PARAM_INT);
        $stmt->bindParam(":quantidade", $quantidade, PDO::PARAM_INT);
        $stmt->bindParam(":preco", $preco, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            $pdo = $database->getInstance();
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Ler todos os registros
    public function readAll() {
        try {
            $sql = "SELECT pp.*, prod.nome_produto 
                    FROM `{$this->tabela}` pp 
                    INNER JOIN produto prod ON pp.id_produto = prod.id_produto
                    ORDER BY pp.id_produto_pedido DESC";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos do pedido: " . $e->getMessage());
        }
    }
    
    // Ler por ID do pedido
    public function readByPedido($id_pedido) {
        try {
            $sql = "SELECT pp.*, prod.nome_produto, prod.imagem 
                    FROM `{$this->tabela}` pp 
                    INNER JOIN produto prod ON pp.id_produto = prod.id_produto
                    WHERE pp.id_pedido = :id_pedido
                    ORDER BY prod.nome_produto";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos do pedido: " . $e->getMessage());
        }
    }
}
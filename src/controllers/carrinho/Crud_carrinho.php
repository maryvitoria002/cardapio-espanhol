<?php 

require_once __DIR__ . '\Carrinho.php';

class Crud_carrinho extends Carrinho{

    /*
    CREATE TABLE `carrinho` (
        `id_carrinho` int NOT NULL AUTO_INCREMENT,
        `quantidade` int NOT NULL,
        `id_usuario` int NOT NULL,
        `id_produto` int NOT NULL,
        PRIMARY KEY (`id_carrinho`),
        KEY `id_usuario` (`id_usuario`),
        KEY `id_produto` (`id_produto`),
        CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
        CONSTRAINT `carrinho_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produto` (`id_produto`)
    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    */
    // CRUD CRIAR (CREATE)
    public function create() {
        $quantidade = $this->getQuantidade();
        $id_usuario = $this->getId_usuario();
        $id_produto = $this->getId_produto();

        try {
            // Valida quantidade
            $this->validaQuantidade($quantidade);
            
            // Verifica estoque
            if (!$this->verificaEstoque($id_produto, $quantidade)) {
                throw new Exception("Quantidade solicitada não disponível em estoque");
            }

            $sql = "INSERT INTO `{$this->tabela}` (quantidade, id_usuario, id_produto) VALUES (:quantidade, :id_usuario, :id_produto)";

            // Criando uma instância para o bd e prepare statement
            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(":quantidade", $quantidade, PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);
            $stmt->bindParam(":id_produto", $id_produto, PDO::PARAM_INT);

            $stmt->execute();
            echo "<script>alert('Item adicionado ao carrinho!'); window.location.href = './carrinho.php';</script>";
            exit();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    
    // CRUD LER (READ)
    public function read(){;
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

    // CRUD LER TODOS (READ ALL)
    public function readAll() {
        $id_usuario = $this->getId_usuario();
        $sql = "SELECT c.*, p.nome, p.preco, p.imagem 
                FROM `{$this->tabela}` c 
                INNER JOIN produto p ON c.id_produto = p.id 
                WHERE c.email = :email";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar itens do carrinho: " . $e->getMessage());
        }
    }

    // CRUD ATUALIZAR (UPDATE)
    public function update() {
        $quantidade = $this->getQuantidade();
        $id_usuario = $this->getId_usuario();
        $id_produto = $this->getId_produto();

        try {
            // Valida quantidade
            $this->validaQuantidade($quantidade);
            
            // Verifica estoque
            if (!$this->verificaEstoque($id_produto, $quantidade)) {
                throw new Exception("Quantidade solicitada não disponível em estoque");
            }

            $sql = "UPDATE `{$this->tabela}` 
                   SET quantidade = :quantidade 
                   WHERE id_usuario = :id_usuario AND id_produto = :id_produto";

            $database = new Database();
            $stmt = $database->prepare($sql);
            $stmt->bindParam(":quantidade", $quantidade, PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);
            $stmt->bindParam(":id_produto", $id_produto, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->rowCount() > 0;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // CRUD DELETAR (DELETE)
    public function delete() {
        $id_usuario= $this->getId_usuario();
        $id_produto = $this->getId_produto();

        $sql = "DELETE FROM `{$this->tabela}` 
                WHERE id_usuario = :id_usuario AND id_produto = :id_produto";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);
        $stmt->bindParam(":id_produto", $id_produto, PDO::PARAM_INT);

        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao remover item do carrinho: " . $e->getMessage());
        }
    }

    // Método adicional para limpar o carrinho
    public function clearCart() {
        $id_usuario = $this->getId_usuario();
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_usuario = :id_usuario";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro ao limpar carrinho: " . $e->getMessage());
        }
    }

    // Método para calcular o total do carrinho
    public function getCartTotal() {
        $id_usuario = $this->getId_usuario();
        $sql = "SELECT SUM(c.quantidade * p.preco) as total 
                FROM `{$this->tabela}` c 
                INNER JOIN produtos p ON c.id_produto = p.id 
                WHERE c.id_usuario = :id_usuario";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular total do carrinho: " . $e->getMessage());
        }
    }

    // Método para verificar se o estoque tá disponível
    private function verificaEstoque($id_produto, $quantidade_solicitada) {
        $sql = "SELECT estoque FROM produtos WHERE id = :id_produto";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_produto", $id_produto, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception("Produto não encontrado");
            }
            
            return $result['estoque'] >= $quantidade_solicitada;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar estoque: " . $e->getMessage());
        }
    }

    // Método para validar quantidade (pq temq ser positiva e não pode ultrapassar um limite)
    private function validaQuantidade($quantidade, $max_por_pedido = 10) {
        if ($quantidade < 1) {
            throw new Exception("Quantidade mínima é 1");
        }
        if ($quantidade > $max_por_pedido) {
            throw new Exception("Quantidade máxima por pedido é {$max_por_pedido}");
        }
        return true;
    }

    // Método para calcular subtotal de um item (especifico)
    public function getItemSubtotal($id_produto, $quantidade) {
        $sql = "SELECT preco FROM produtos WHERE id = :id_produto";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":id_produto", $id_produto, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception("Produto não encontrado");
            }
            
            return $result['preco'] * $quantidade;
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular subtotal: " . $e->getMessage());
        }
    }
}
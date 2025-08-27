<?php 

require_once "./Carrinho.php";

class CrudCarrinho extends Carrinho{
 
    // CRUD CRIAR (CREATE)
    public function create(){
        $email = $this->getEmail();
        $id_produto = $this->getIdProduto();
        $quantidade = $this->getQuantidade();
        $sql = "INSERT INTO `{$this->tabela}` (email, id_produto, quantidade) VALUES (:email, :id_produto, :quantidade)";

        // Criando uma instância para o bd e prepare statement
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":id_produto", $id_produto, PDO::PARAM_INT);
        $stmt->bindParam(":quantidade", $quantidade, PDO::PARAM_INT);
 
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

    // CRUD LER TODOS (READ ALL)
    public function readAll() {
        $email = $this->getEmail();
        $sql = "SELECT c.*, p.nome, p.preco, p.imagem 
                FROM `{$this->tabela}` c 
                INNER JOIN produtos p ON c.id_produto = p.id 
                WHERE c.email = :email";
        
        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar itens do carrinho: " . $e->getMessage());
        }
    }

    // CRUD ATUALIZAR (UPDATE)
    public function update() {
        $email = $this->getEmail();
        $id_produto = $this->getIdProduto();
        $quantidade = $this->getQuantidade();

        $sql = "UPDATE `{$this->tabela}` 
                SET quantidade = :quantidade 
                WHERE email = :email AND id_produto = :id_produto";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":quantidade", $quantidade, PDO::PARAM_INT);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":id_produto", $id_produto, PDO::PARAM_INT);

        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar quantidade: " . $e->getMessage());
        }
    }

    // CRUD DELETAR (DELETE)
    public function delete() {
        $email = $this->getEmail();
        $id_produto = $this->getIdProduto();

        $sql = "DELETE FROM `{$this->tabela}` 
                WHERE email = :email AND id_produto = :id_produto";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
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
        $email = $this->getEmail();
        $sql = "DELETE FROM `{$this->tabela}` WHERE email = :email";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro ao limpar carrinho: " . $e->getMessage());
        }
    }

    // Método para calcular o total do carrinho
    public function getCartTotal() {
        $email = $this->getEmail();
        $sql = "SELECT SUM(c.quantidade * p.preco) as total 
                FROM `{$this->tabela}` c 
                INNER JOIN produtos p ON c.id_produto = p.id 
                WHERE c.email = :email";

        $database = new Database();
        $stmt = $database->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao calcular total do carrinho: " . $e->getMessage());
        }
    }
}
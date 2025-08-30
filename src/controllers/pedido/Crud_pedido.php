<?php 

require_once "./Pedido.php";

class CrudPedido extends Pedido{
 
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
}
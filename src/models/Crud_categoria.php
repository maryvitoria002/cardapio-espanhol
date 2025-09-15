<?php 

require_once "../controllers/Categoria.php";

class Crud_categoria extends Categoria {
 
    // CRUD CRIAR (CREATE)
    public function create(){
        $nome_categoria = $this->getNome_categoria();
        $data_criacao = $this->getData_criacao();
        
        $sql = "INSERT INTO `{$this->tabela}` (nome_categoria, data_criacao) 
                VALUES (:nome_categoria, :data_criacao)";

        // Usando o método prepare da classe pai Database
        $stmt = $this->prepare($sql);
        $stmt->bindParam(":nome_categoria", $nome_categoria, PDO::PARAM_STR);
        $stmt->bindParam(":data_criacao", $data_criacao, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // CRUD LER (READ)
    public function read(){
        $id_categoria = $this->getId_categoria();
        
        if ($id_categoria) {
            // Se há ID definido, buscar categoria específica
            $sql = "SELECT * FROM `{$this->tabela}` WHERE id_categoria = :id_categoria";
            
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_categoria", $id_categoria, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Se não há ID, buscar todas as categorias
            $sql = "SELECT * FROM `{$this->tabela}` ORDER BY nome_categoria";
            
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    // CRUD ATUALIZAR (UPDATE)
    public function update(){
        $id_categoria = $this->getId_categoria();
        $nome_categoria = $this->getNome_categoria();
        
        $sql = "UPDATE `{$this->tabela}` SET 
                nome_categoria = :nome_categoria
                WHERE id_categoria = :id_categoria";

        $stmt = $this->prepare($sql);
        $stmt->bindParam(":nome_categoria", $nome_categoria, PDO::PARAM_STR);
        $stmt->bindParam(":id_categoria", $id_categoria, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // CRUD DELETAR (DELETE)
    public function delete(){
        $id_categoria = $this->getId_categoria();
        $sql = "DELETE FROM `{$this->tabela}` WHERE id_categoria = :id_categoria";
        
        $stmt = $this->prepare($sql);
        $stmt->bindParam(":id_categoria", $id_categoria, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        }
    }
    
    // Método para buscar categoria por nome
    public function readByName($nome_categoria){
        $sql = "SELECT * FROM `{$this->tabela}` WHERE nome_categoria = :nome_categoria";
        
        $stmt = $this->prepare($sql);
        $stmt->bindParam(":nome_categoria", $nome_categoria, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Método para contar produtos por categoria
    public function countProdutos($id_categoria){
        $sql = "SELECT COUNT(*) as total FROM produto WHERE id_categoria = :id_categoria";
        
        $stmt = $this->prepare($sql);
        $stmt->bindParam(":id_categoria", $id_categoria, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Método para listar todas as categorias
    public function readAll(){
        $sql = "SELECT * FROM `{$this->tabela}` ORDER BY nome_categoria";
        
        $stmt = $this->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Método para contar total de categorias
    public function count(){
        $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}`";
        
        $stmt = $this->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Método para buscar por ID específico
    public function readById($id_categoria){
        $sql = "SELECT * FROM `{$this->tabela}` WHERE id_categoria = :id_categoria";
        
        $stmt = $this->prepare($sql);
        $stmt->bindParam(":id_categoria", $id_categoria, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Verificar se nome da categoria já existe
    public function nomeExists($nome_categoria, $id_categoria = null){
        if ($id_categoria) {
            // Para edição - excluir o próprio ID da verificação
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` 
                    WHERE nome_categoria = :nome_categoria AND id_categoria != :id_categoria";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":nome_categoria", $nome_categoria, PDO::PARAM_STR);
            $stmt->bindParam(":id_categoria", $id_categoria, PDO::PARAM_INT);
        } else {
            // Para criação - verificar se nome existe
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` 
                    WHERE nome_categoria = :nome_categoria";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":nome_categoria", $nome_categoria, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
    
    // Alias para readAll para compatibilidade
    public function getAll(){
        return $this->readAll();
    }
    
    // Métodos setter para compatibilidade com formulários
    public function setNomeCategoria($nome_categoria){
        $this->setNome_categoria($nome_categoria);
    }
    
    public function setDescricao($descricao){
        // Método para compatibilidade - pode implementar se houver campo descrição
        // Por enquanto apenas retorna true
        return true;
    }
}

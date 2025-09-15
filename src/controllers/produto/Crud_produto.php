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
    public function count($search = '', $categoria_filter = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM `{$this->tabela}` p";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(p.nome_produto LIKE :search OR p.descricao LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($categoria_filter)) {
                $whereConditions[] = "p.id_categoria = :categoria";
                $params['categoria'] = $categoria_filter;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $database = new Database();
            $conexao = $database->getInstance();
            $stmt = $conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
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

    // Método para buscar todos os produtos com paginação e filtros
    public function readAll($search = '', $categoria_filter = '', $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT p.*, c.nome_categoria FROM `{$this->tabela}` p 
                    LEFT JOIN categoria c ON p.id_categoria = c.id_categoria";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(p.nome_produto LIKE :search OR p.descricao LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($categoria_filter)) {
                $whereConditions[] = "p.id_categoria = :categoria";
                $params['categoria'] = $categoria_filter;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY p.data_criacao DESC LIMIT :offset, :per_page";
            
            $database = new Database();
            $conexao = $database->getInstance();
            $stmt = $conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos: " . $e->getMessage());
        }
    }

    // Método para upload de imagem de produto
    public function uploadImagem($arquivo, $imagem_anterior = null) {
        try {
            // Verificar se houve erro no upload
            if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Erro no upload do arquivo');
            }

            // Verificar tamanho do arquivo (máximo 5MB)
            $tamanho_max = 5 * 1024 * 1024; // 5MB
            if ($arquivo['size'] > $tamanho_max) {
                throw new Exception('Arquivo muito grande. Máximo 5MB');
            }

            // Verificar tipo do arquivo
            $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $tipo_arquivo = finfo_file($finfo, $arquivo['tmp_name']);
            finfo_close($finfo);

            if (!in_array($tipo_arquivo, $tipos_permitidos)) {
                throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG ou GIF');
            }

            // Gerar nome único para o arquivo
            $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
            $nome_arquivo = 'produto_' . uniqid() . '_' . time() . '.' . $extensao;
            
            // Definir caminho de destino
            $diretorio_destino = __DIR__ . '/../../images/comidas/';
            
            // Criar diretório se não existir
            if (!is_dir($diretorio_destino)) {
                mkdir($diretorio_destino, 0755, true);
            }
            
            $caminho_completo = $diretorio_destino . $nome_arquivo;

            // Mover arquivo para o destino
            if (!move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
                throw new Exception('Erro ao salvar arquivo');
            }

            // Remover imagem anterior se existir
            if ($imagem_anterior && file_exists($diretorio_destino . $imagem_anterior)) {
                unlink($diretorio_destino . $imagem_anterior);
            }

            return $nome_arquivo;

        } catch (Exception $e) {
            throw new Exception("Erro no upload da imagem: " . $e->getMessage());
        }
    }
}
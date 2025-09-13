<?php
require_once "Produto.php";

class Crud_produto extends Produto {
    
    // Obter todos os produtos com busca e paginação
    public function readAll($search = '', $categoria = '', $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT p.*, c.nome_categoria 
                    FROM produto p 
                    LEFT JOIN categoria c ON p.id_categoria = c.id_categoria";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(p.nome_produto LIKE :search OR p.descricao LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($categoria)) {
                $whereConditions[] = "p.id_categoria = :categoria";
                $params['categoria'] = $categoria;
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY p.id_produto DESC LIMIT :offset, :perPage";
            
            $stmt = $this->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos: " . $e->getMessage());
        }
    }
    
    // Contar total de produtos com busca
    public function count($search = '', $categoria = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM produto p";
            $params = [];
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(p.nome_produto LIKE :search OR p.descricao LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            if (!empty($categoria)) {
                $whereConditions[] = "p.id_categoria = :categoria";
                $params['categoria'] = $categoria;
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
            throw new Exception("Erro ao contar produtos: " . $e->getMessage());
        }
    }
    
    // Buscar produto por ID
    public function readById($id) {
        try {
            $sql = "SELECT p.*, c.nome_categoria 
                    FROM produto p 
                    LEFT JOIN categoria c ON p.id_categoria = c.id_categoria 
                    WHERE p.id_produto = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produto: " . $e->getMessage());
        }
    }
    
    // Criar novo produto
    public function create($nome_produto, $descricao, $preco, $id_categoria, $imagem, $ativo = 1) {
        try {
            $status = $ativo ? 'Disponivel' : 'Indisponivel';
            
            $sql = "INSERT INTO produto (nome_produto, descricao, preco, id_categoria, imagem, status, estoque, data_criacao) 
                    VALUES (:nome_produto, :descricao, :preco, :id_categoria, :imagem, :status, 0, NOW())";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nome_produto', $nome_produto);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':preco', $preco);
            $stmt->bindValue(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $stmt->bindValue(':imagem', $imagem);
            $stmt->bindValue(':status', $status);
            
            $stmt->execute();
            
            return true;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar produto: " . $e->getMessage());
        }
    }
    
    // Atualizar produto
    public function update($id, $nome_produto, $descricao, $preco, $id_categoria, $imagem = null, $ativo = 1) {
        try {
            $status = $ativo ? 'Disponivel' : 'Indisponivel';
            
            if ($imagem) {
                $sql = "UPDATE produto SET 
                        nome_produto = :nome_produto,
                        descricao = :descricao,
                        preco = :preco,
                        id_categoria = :id_categoria,
                        imagem = :imagem,
                        status = :status,
                        data_atualizacao = NOW()
                        WHERE id_produto = :id";
            } else {
                $sql = "UPDATE produto SET 
                        nome_produto = :nome_produto,
                        descricao = :descricao,
                        preco = :preco,
                        id_categoria = :id_categoria,
                        status = :status,
                        data_atualizacao = NOW()
                        WHERE id_produto = :id";
            }
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nome_produto', $nome_produto);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':preco', $preco);
            $stmt->bindValue(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            if ($imagem) {
                $stmt->bindValue(':imagem', $imagem);
            }
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar produto: " . $e->getMessage());
        }
    }
    
    // Deletar produto
    public function delete($id) {
        try {
            // Primeiro, obter a imagem para deletar o arquivo
            $produto = $this->readById($id);
            
            $sql = "DELETE FROM produto WHERE id_produto = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            // Se deletou com sucesso e tinha imagem, deletar o arquivo
            if ($result && !empty($produto['imagem'])) {
                $imagePath = __DIR__ . "/../../images/comidas/" . $produto['imagem'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            return $result;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao deletar produto: " . $e->getMessage());
        }
    }
    
    // Upload de imagem
    public function uploadImagem($file, $oldImage = null) {
        $uploadDir = __DIR__ . "/../../images/comidas/";
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Criar diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erro no upload da imagem.");
        }
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Tipo de arquivo não permitido. Use JPEG, PNG ou GIF.");
        }
        
        if ($file['size'] > $maxSize) {
            throw new Exception("Arquivo muito grande. Máximo 5MB.");
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Fazer upload da imagem original
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Erro ao salvar imagem.");
        }
        
        // Redimensionar imagem para 400x300 (mantendo proporção)
        $this->redimensionarImagem($filepath, 400, 300);
        
        // Remover imagem antiga se existir
        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }
        
        return $filename;
    }
    
    // Redimensionar imagem
    private function redimensionarImagem($filepath, $maxWidth, $maxHeight) {
        $imageInfo = getimagesize($filepath);
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $type = $imageInfo[2];
        
        // Calcular novas dimensões mantendo proporção
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = intval($originalWidth * $ratio);
        $newHeight = intval($originalHeight * $ratio);
        
        // Criar imagem de origem
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filepath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filepath);
                break;
            default:
                throw new Exception("Tipo de imagem não suportado.");
        }
        
        // Criar imagem de destino
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Manter transparência para PNG e GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Redimensionar
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Salvar imagem redimensionada
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $filepath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $filepath);
                break;
            case IMAGETYPE_GIF:
                imagegif($destination, $filepath);
                break;
        }
        
        // Limpar memória
        imagedestroy($source);
        imagedestroy($destination);
    }
    
    // Atualizar estoque
    public function updateEstoque($id, $quantidade) {
        try {
            $sql = "UPDATE produto SET estoque = estoque + :quantidade WHERE id_produto = :id";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar estoque: " . $e->getMessage());
        }
    }
    
    // Buscar produtos por categoria
    public function readByCategoria($idCategoria) {
        try {
            $sql = "SELECT p.*, c.nome_categoria 
                    FROM produto p 
                    LEFT JOIN categoria c ON p.id_categoria = c.id_categoria 
                    WHERE p.id_categoria = :id_categoria 
                    ORDER BY p.nome_produto ASC";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id_categoria', $idCategoria, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos da categoria: " . $e->getMessage());
        }
    }
    
    // Contar produtos por status
    public function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) as total FROM produto WHERE status = :status";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar produtos por status: " . $e->getMessage());
        }
    }
    
    // Produtos mais vendidos
    public function getMaisVendidos($limit = 5) {
        try {
            $sql = "SELECT p.*, c.nome_categoria,
                           COALESCE(SUM(pp.quantidade), 0) as total_vendido
                    FROM produto p 
                    LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
                    LEFT JOIN produto_pedido pp ON p.id_produto = pp.id_produto
                    LEFT JOIN pedido pd ON pp.id_pedido = pd.id_pedido 
                    WHERE pd.status_pedido = 'Concluido'
                    GROUP BY p.id_produto
                    ORDER BY total_vendido DESC, p.nome_produto ASC
                    LIMIT :limit";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar produtos mais vendidos: " . $e->getMessage());
        }
    }
}
?>

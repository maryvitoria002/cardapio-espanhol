<?php
require_once __DIR__ . '/../../db/conection.php';
require_once __DIR__ . '/../controllers/Favorito.php';

class Crud_favorito {
    private $conexao;

    public function __construct() {
        $database = new Database();
        $this->conexao = $database->getInstance();
    }

    // Adicionar produto aos favoritos
    public function adicionarFavorito($id_usuario, $id_produto) {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO favoritos (id_usuario, id_produto) VALUES (?, ?)");
            $result = $stmt->execute([$id_usuario, $id_produto]);
            return $result;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Duplicate entry
                return false; // Já é favorito
            }
            throw new Exception("Erro ao adicionar favorito: " . $e->getMessage());
        }
    }

    // Remover produto dos favoritos
    public function removerFavorito($id_usuario, $id_produto) {
        try {
            $stmt = $this->conexao->prepare("DELETE FROM favoritos WHERE id_usuario = ? AND id_produto = ?");
            return $stmt->execute([$id_usuario, $id_produto]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao remover favorito: " . $e->getMessage());
        }
    }

    // Verificar se produto é favorito do usuário
    public function isFavorito($id_usuario, $id_produto) {
        try {
            $stmt = $this->conexao->prepare("SELECT COUNT(*) FROM favoritos WHERE id_usuario = ? AND id_produto = ?");
            $stmt->execute([$id_usuario, $id_produto]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar favorito: " . $e->getMessage());
        }
    }

    // Buscar todos os favoritos de um usuário
    public function getFavoritosByUsuario($id_usuario) {
        try {
            $stmt = $this->conexao->prepare("
                SELECT f.*, p.nome_produto, p.preco, p.descricao, p.imagem, p.id_categoria 
                FROM favoritos f 
                INNER JOIN produto p ON f.id_produto = p.id_produto 
                WHERE f.id_usuario = ? 
                ORDER BY f.data_criacao DESC
            ");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar favoritos: " . $e->getMessage());
        }
    }

    // Contar favoritos de um usuário
    public function contarFavoritos($id_usuario) {
        try {
            $stmt = $this->conexao->prepare("SELECT COUNT(*) FROM favoritos WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar favoritos: " . $e->getMessage());
        }
    }

    // Toggle favorito (adiciona se não existe, remove se existe)
    public function toggleFavorito($id_usuario, $id_produto) {
        try {
            if ($this->isFavorito($id_usuario, $id_produto)) {
                $this->removerFavorito($id_usuario, $id_produto);
                return ['action' => 'removed', 'message' => 'Produto removido dos favoritos'];
            } else {
                $this->adicionarFavorito($id_usuario, $id_produto);
                return ['action' => 'added', 'message' => 'Produto adicionado aos favoritos'];
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao alterar favorito: " . $e->getMessage());
        }
    }
}
?>
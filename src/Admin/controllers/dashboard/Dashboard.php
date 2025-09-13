<?php
require_once __DIR__ . "/../../../db/conection.php";

class Dashboard extends Database {
    
    // Obter estatísticas gerais
    public function getStats() {
        try {
            $stats = [];
            
            // Total de usuários
            $stmt = $this->prepare("SELECT COUNT(*) as total FROM usuario");
            $stmt->execute();
            $stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total de produtos
            $stmt = $this->prepare("SELECT COUNT(*) as total FROM produto");
            $stmt->execute();
            $stats['produtos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total de pedidos
            $stmt = $this->prepare("SELECT COUNT(*) as total FROM pedido");
            $stmt->execute();
            $stats['pedidos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total de avaliações
            $stmt = $this->prepare("SELECT COUNT(*) as total FROM avaliacao");
            $stmt->execute();
            $stats['avaliacoes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return $stats;
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter estatísticas: " . $e->getMessage());
        }
    }
    
    // Obter pedidos recentes
    public function getPedidosRecentes($limit = 5) {
        try {
            $sql = "
                SELECT p.id_pedido, u.primeiro_nome, u.segundo_nome, p.status_pedido, 
                       p.data_pedido, p.endereco
                FROM pedido p 
                JOIN usuario u ON p.id_usuario = u.id_usuario 
                ORDER BY p.data_pedido DESC 
                LIMIT :limit
            ";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter pedidos recentes: " . $e->getMessage());
        }
    }
    
    // Obter vendas por mês
    public function getVendasMensais() {
        try {
            $sql = "
                SELECT 
                    MONTH(p.data_pedido) as mes,
                    YEAR(p.data_pedido) as ano,
                    COUNT(*) as total_pedidos,
                    SUM(pp.quantidade * pp.preco) as total_vendas
                FROM pedido p
                LEFT JOIN produto_pedido pp ON p.id_pedido = pp.id_pedido
                WHERE p.status_pedido = 'Concluido'
                AND p.data_pedido >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY YEAR(p.data_pedido), MONTH(p.data_pedido)
                ORDER BY ano DESC, mes DESC
            ";
            
            $stmt = $this->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter vendas mensais: " . $e->getMessage());
        }
    }
    
    // Obter produtos mais vendidos
    public function getProdutosMaisVendidos($limit = 5) {
        try {
            $sql = "
                SELECT 
                    pr.nome_produto,
                    pr.imagem,
                    SUM(pp.quantidade) as total_vendido,
                    SUM(pp.quantidade * pp.preco) as total_receita
                FROM produto_pedido pp
                JOIN produto pr ON pp.id_produto = pr.id_produto
                JOIN pedido p ON pp.id_pedido = p.id_pedido
                WHERE p.status_pedido = 'Concluido'
                GROUP BY pr.id_produto
                ORDER BY total_vendido DESC
                LIMIT :limit
            ";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter produtos mais vendidos: " . $e->getMessage());
        }
    }
    
    // Obter avaliações recentes
    public function getAvaliacoesRecentes($limit = 5) {
        try {
            $sql = "
                SELECT 
                    a.nota,
                    a.texto_avaliacao,
                    a.data_avaliacao,
                    a.status,
                    u.primeiro_nome,
                    u.segundo_nome
                FROM avaliacao a
                JOIN usuario u ON a.id_usuario = u.id_usuario
                ORDER BY a.data_avaliacao DESC
                LIMIT :limit
            ";
            
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter avaliações recentes: " . $e->getMessage());
        }
    }
}
?>

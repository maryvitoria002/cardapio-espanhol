<?php
require_once 'conection.php';

try {
    $database = new Database();
    $pdo = $database->getInstance();
    
    echo "Conectado ao banco de dados com sucesso!\n";
    
    // 1. Remover tabela favoritos existente (se houver)
    $pdo->exec("DROP TABLE IF EXISTS favoritos");
    echo "Tabela 'favoritos' anterior removida.\n";
    
    // 2. Verificar se as tabelas de referência existem
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas existentes: " . implode(', ', $tables) . "\n";
    
    if (!in_array('usuario', $tables)) {
        throw new Exception("Tabela 'usuario' não encontrada!");
    }
    
    if (!in_array('produto', $tables)) {
        throw new Exception("Tabela 'produto' não encontrada!");
    }
    
    // 3. Criar tabela favoritos com referências corretas
    $sqlFavoritos = "
        CREATE TABLE favoritos (
            id_favorito INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            id_produto INT NOT NULL,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_product (id_usuario, id_produto),
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_produto) REFERENCES produto(id_produto) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ";
    
    $pdo->exec($sqlFavoritos);
    echo "Tabela 'favoritos' criada com sucesso com foreign keys corretas!\n";
    
    // 4. Verificar estrutura da nova tabela
    echo "\nEstrutura da tabela favoritos:\n";
    $columns = $pdo->query("DESCRIBE favoritos")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    // 5. Verificar foreign keys
    echo "\nForeign Keys da tabela favoritos:\n";
    $fks = $pdo->query("
        SELECT 
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'favoritos' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($fks as $fk) {
        echo "- " . $fk['CONSTRAINT_NAME'] . ": " . $fk['COLUMN_NAME'] . " -> " . $fk['REFERENCED_TABLE_NAME'] . "." . $fk['REFERENCED_COLUMN_NAME'] . "\n";
    }
    
    echo "\nScript executado com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
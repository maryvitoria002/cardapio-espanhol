<?php
require_once __DIR__ . '/conection.php';

try {
    $database = new Database();
    $pdo = $database->getInstance();
    
    echo "Conectado ao banco de dados com sucesso!\n";
    
    // Verificar tabelas existentes
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas existentes: " . implode(', ', $tables) . "\n";
    
    // Verificar estrutura da tabela usuario
    if (in_array('usuario', $tables)) {
        $columns = $pdo->query("DESCRIBE usuario")->fetchAll(PDO::FETCH_ASSOC);
        echo "Colunas da tabela usuario:\n";
        foreach ($columns as $col) {
            echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    }
    
    // Verificar estrutura da tabela produtos
    if (in_array('produtos', $tables)) {
        $columns = $pdo->query("DESCRIBE produtos")->fetchAll(PDO::FETCH_ASSOC);
        echo "Colunas da tabela produtos:\n";
        foreach ($columns as $col) {
            echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    }
    
    // 1. Adicionar coluna foto_perfil na tabela usuario (se não existir)
    if (in_array('usuario', $tables)) {
        $sqlCheckColumn = "SHOW COLUMNS FROM usuario LIKE 'foto_perfil'";
        $stmt = $pdo->prepare($sqlCheckColumn);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            $sqlAddColumn = "ALTER TABLE usuario ADD COLUMN foto_perfil VARCHAR(255) NULL";
            $pdo->exec($sqlAddColumn);
            echo "Coluna 'foto_perfil' adicionada na tabela usuario!\n";
        } else {
            echo "Coluna 'foto_perfil' já existe na tabela usuario.\n";
        }
        
        // Verificar e corrigir tamanho da coluna senha
        $sqlCheckSenha = "SHOW COLUMNS FROM usuario LIKE 'senha'";
        $stmt = $pdo->prepare($sqlCheckSenha);
        $stmt->execute();
        $senhaColumn = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($senhaColumn && strpos($senhaColumn['Type'], 'varchar(50)') !== false) {
            $sqlFixSenha = "ALTER TABLE usuario MODIFY COLUMN senha VARCHAR(255) NOT NULL";
            $pdo->exec($sqlFixSenha);
            echo "Coluna 'senha' expandida para VARCHAR(255)!\n";
        } else {
            echo "Coluna 'senha' já tem tamanho adequado.\n";
        }
    }
    
    // 2. Criar tabela de favoritos (com foreign keys corretas)
    $sqlFavoritos = "
        CREATE TABLE IF NOT EXISTS favoritos (
            id_favorito INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            id_produto INT NOT NULL,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_product (id_usuario, id_produto),
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_produto) REFERENCES produto(id_produto) ON DELETE CASCADE
        )
    ";
    
    $pdo->exec($sqlFavoritos);
    echo "Tabela 'favoritos' criada com sucesso!\n";
    
    echo "Script executado com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
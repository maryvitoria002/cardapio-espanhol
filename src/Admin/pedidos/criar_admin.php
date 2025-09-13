<?php
require_once __DIR__ . '/../../db/conection.php';

// Criar instância da conexão
$database = new Database();
$conexao = $database->getInstance();

try {
    // Verificar se a tabela funcionario existe
    $stmt = $conexao->query("SHOW TABLES LIKE 'funcionario'");
    $funcionarioExists = $stmt->rowCount() > 0;
    
    if ($funcionarioExists) {
        echo "Tabela funcionario encontrada.<br>";
        
        // Verificar estrutura da tabela funcionario
        $stmt = $conexao->query("DESCRIBE funcionario");
        echo "<h3>Estrutura da tabela funcionario:</h3>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
        
        // Criar conta admin
        $email = "maryleloli1811@gmail.com";
        $senha = password_hash("123", PASSWORD_DEFAULT);
        $nome = "Mary";
        $sobrenome = "Admin";
        $nivel_acesso = "admin";
        
        // Verificar se já existe
        $stmt = $conexao->prepare("SELECT COUNT(*) as total FROM funcionario WHERE email = ?");
        $stmt->execute([$email]);
        $exists = $stmt->fetch()['total'];
        
        if ($exists > 0) {
            echo "<br><strong>Admin já existe! Atualizando senha...</strong><br>";
            $stmt = $conexao->prepare("UPDATE funcionario SET senha = ? WHERE email = ?");
            $stmt->execute([$senha, $email]);
        } else {
            echo "<br><strong>Criando nova conta admin...</strong><br>";
            $stmt = $conexao->prepare("INSERT INTO funcionario (nome, sobrenome, email, senha, nivel_acesso, data_criacao) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$nome, $sobrenome, $email, $senha, $nivel_acesso]);
        }
        
        echo "<strong>✅ Conta admin criada/atualizada com sucesso!</strong><br>";
        echo "Email: maryleloli1811@gmail.com<br>";
        echo "Senha: 123<br>";
        echo "<br><a href='../login.php'>Fazer Login</a>";
        
    } else {
        echo "Tabela funcionario não encontrada. Verificando outras tabelas...<br>";
        
        // Verificar se existe tabela admin
        $stmt = $conexao->query("SHOW TABLES LIKE 'admin'");
        $adminExists = $stmt->rowCount() > 0;
        
        if ($adminExists) {
            echo "Usando tabela admin...<br>";
            
            $email = "maryleloli1811@gmail.com";
            $senha = password_hash("123", PASSWORD_DEFAULT);
            
            $stmt = $conexao->prepare("SELECT COUNT(*) as total FROM admin WHERE email = ?");
            $stmt->execute([$email]);
            $exists = $stmt->fetch()['total'];
            
            if ($exists > 0) {
                $stmt = $conexao->prepare("UPDATE admin SET senha = ? WHERE email = ?");
                $stmt->execute([$senha, $email]);
            } else {
                $stmt = $conexao->prepare("INSERT INTO admin (email, senha) VALUES (?, ?)");
                $stmt->execute([$email, $senha]);
            }
            
            echo "✅ Admin criado na tabela admin!<br>";
        } else {
            echo "Criando tabela funcionario...<br>";
            
            $sql = "CREATE TABLE funcionario (
                id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                sobrenome VARCHAR(100) NOT NULL,
                email VARCHAR(150) UNIQUE NOT NULL,
                senha VARCHAR(255) NOT NULL,
                nivel_acesso ENUM('admin', 'funcionario') DEFAULT 'funcionario',
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            $conexao->exec($sql);
            echo "Tabela funcionario criada!<br>";
            
            // Criar admin
            $email = "maryleloli1811@gmail.com";
            $senha = password_hash("123", PASSWORD_DEFAULT);
            
            $stmt = $conexao->prepare("INSERT INTO funcionario (nome, sobrenome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(["Mary", "Admin", $email, $senha, "admin"]);
            
            echo "✅ Admin criado com sucesso!<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>

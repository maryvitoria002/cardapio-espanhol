<?php
require_once __DIR__ . '/../db/conection.php';

// Criar instância da conexão
$database = new Database();
$conexao = $database->getInstance();

try {
    echo "<h2>Verificando e criando conta admin...</h2>";
    
    // Verificar se a tabela funcionario existe
    $stmt = $conexao->query("SHOW TABLES LIKE 'funcionario'");
    $funcionarioExists = $stmt->rowCount() > 0;
    
    if (!$funcionarioExists) {
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
        echo "✅ Tabela funcionario criada!<br>";
    } else {
        echo "✅ Tabela funcionario já existe.<br>";
    }
    
    // Verificar estrutura da tabela
    $stmt = $conexao->query("DESCRIBE funcionario");
    echo "<h3>Estrutura da tabela funcionario:</h3>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
    $email = "maryleloli1811@gmail.com";
    $senha_texto = "123";
    $senha_hash = password_hash($senha_texto, PASSWORD_DEFAULT);
    
    // Verificar se o admin já existe
    $stmt = $conexao->prepare("SELECT * FROM funcionario WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<br><h3>Admin já existe! Atualizando senha...</h3>";
        echo "ID: " . $admin['id_funcionario'] . "<br>";
        echo "Nome: " . $admin['nome'] . " " . $admin['sobrenome'] . "<br>";
        echo "Email: " . $admin['email'] . "<br>";
        echo "Nível: " . $admin['nivel_acesso'] . "<br>";
        
        // Atualizar senha
        $stmt = $conexao->prepare("UPDATE funcionario SET senha = ?, nivel_acesso = 'admin' WHERE email = ?");
        $stmt->execute([$senha_hash, $email]);
        echo "✅ Senha atualizada!<br>";
    } else {
        echo "<br><h3>Criando nova conta admin...</h3>";
        $stmt = $conexao->prepare("INSERT INTO funcionario (nome, sobrenome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(["Mary", "Admin", $email, $senha_hash, "admin"]);
        echo "✅ Admin criado com sucesso!<br>";
    }
    
    // Verificar se foi criado/atualizado corretamente
    $stmt = $conexao->prepare("SELECT * FROM funcionario WHERE email = ?");
    $stmt->execute([$email]);
    $admin_verificacao = $stmt->fetch();
    
    if ($admin_verificacao) {
        echo "<br><h3>✅ Verificação Final:</h3>";
        echo "ID: " . $admin_verificacao['id_funcionario'] . "<br>";
        echo "Nome: " . $admin_verificacao['nome'] . " " . $admin_verificacao['sobrenome'] . "<br>";
        echo "Email: " . $admin_verificacao['email'] . "<br>";
        echo "Nível: " . $admin_verificacao['nivel_acesso'] . "<br>";
        echo "Senha Hash: " . substr($admin_verificacao['senha'], 0, 20) . "...<br>";
        
        // Testar verificação de senha
        if (password_verify($senha_texto, $admin_verificacao['senha'])) {
            echo "✅ Verificação de senha: <strong style='color: green;'>PASSOU</strong><br>";
        } else {
            echo "❌ Verificação de senha: <strong style='color: red;'>FALHOU</strong><br>";
        }
    }
    
    echo "<br><h3>📋 Credenciais de Login:</h3>";
    echo "<strong>Email:</strong> maryleloli1811@gmail.com<br>";
    echo "<strong>Senha:</strong> 123<br>";
    echo "<br><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Fazer Login</a>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>

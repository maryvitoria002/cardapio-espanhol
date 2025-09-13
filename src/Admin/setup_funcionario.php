<?php
require_once __DIR__ . '/../../db/conection.php';

try {
    $database = new Database();
    $pdo = $database->getInstance();
    
    echo "<h1>Verificação de Funcionários Admin</h1>";
    
    // Verificar funcionários existentes
    $stmt = $pdo->prepare("SELECT id_funcionario, primeiro_nome, segundo_nome, email, acesso FROM funcionario");
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Funcionários Existentes:</h2>";
    if (empty($funcionarios)) {
        echo "<p>Nenhum funcionário encontrado.</p>";
        
        // Criar admin padrão
        echo "<h2>Criando Admin Padrão...</h2>";
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO funcionario (primeiro_nome, segundo_nome, email, telefone, acesso, senha) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            'Admin',
            'Sistema',
            'admin@restaurant.com',
            '(11) 99999-9999',
            'Superadmin',
            $senha_hash
        ]);
        
        if ($result) {
            echo "<p style='color: green;'>Admin criado com sucesso!</p>";
            echo "<p><strong>Email:</strong> admin@restaurant.com</p>";
            echo "<p><strong>Senha:</strong> admin123</p>";
        } else {
            echo "<p style='color: red;'>Erro ao criar admin!</p>";
        }
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Acesso</th></tr>";
        foreach ($funcionarios as $func) {
            echo "<tr>";
            echo "<td>" . $func['id_funcionario'] . "</td>";
            echo "<td>" . $func['primeiro_nome'] . " " . $func['segundo_nome'] . "</td>";
            echo "<td>" . $func['email'] . "</td>";
            echo "<td>" . $func['acesso'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
?>

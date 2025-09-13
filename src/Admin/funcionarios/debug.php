<?php
session_start();
echo "<h1>Debug Funcionários</h1>";

// Verificar sessão
echo "<h2>1. Verificação de Sessão:</h2>";
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    echo "<p style='color: red;'>❌ Sessão inválida - redirecionaria para login</p>";
    echo "<pre>Sessões: " . print_r($_SESSION, true) . "</pre>";
} else {
    echo "<p style='color: green;'>✅ Sessão válida</p>";
    echo "<p>Admin: " . $_SESSION['admin_nome'] . " (" . $_SESSION['admin_acesso'] . ")</p>";
}

// Testar inclusão da classe
echo "<h2>2. Teste de Inclusão da Classe:</h2>";
try {
    require_once __DIR__ . '/../../controllers/funcionario/Crud_funcionario.php';
    echo "<p style='color: green;'>✅ Classe Crud_funcionario carregada</p>";
    
    $crudFuncionario = new Crud_funcionario();
    echo "<p style='color: green;'>✅ Instância criada</p>";
    
    // Testar método count simples
    echo "<h2>3. Teste de Método Count:</h2>";
    $total = $crudFuncionario->count();
    echo "<p style='color: green;'>✅ Total de funcionários: " . $total . "</p>";
    
    // Testar método readAll simples
    echo "<h2>4. Teste de ReadAll:</h2>";
    $funcionarios = $crudFuncionario->readAll('', '', 1, 5);
    echo "<p style='color: green;'>✅ Funcionários encontrados: " . count($funcionarios) . "</p>";
    
    if (!empty($funcionarios)) {
        echo "<table border='1'>";
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
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>5. Link para Funcionários:</h2>";
echo "<a href='index.php'>Tentar acessar página de funcionários</a>";
?>

<?php
echo "Teste de acesso à pasta usuarios - OK!<br>";
echo "Caminho atual: " . __DIR__ . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Verificar se os arquivos existem
$files = ['index.php', 'view.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file NÃO existe<br>";
    }
}

// Verificar se as pastas necessárias existem
$folders = ['../controllers/', '../../controllers/'];
foreach ($folders as $folder) {
    if (is_dir($folder)) {
        echo "✅ $folder existe<br>";
    } else {
        echo "❌ $folder NÃO existe<br>";
    }
}
?>

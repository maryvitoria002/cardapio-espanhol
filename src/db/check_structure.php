<?php
require_once 'conection.php';

$database = new Database();
$pdo = $database->getInstance();

echo "Tabelas do banco:\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "- " . $table . "\n";
}

echo "\nEstrutura da tabela usuario:\n";
$columns = $pdo->query("DESCRIBE usuario")->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
}

echo "\nEstrutura da tabela favoritos:\n";
$columns = $pdo->query("DESCRIBE favoritos")->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
}
?>
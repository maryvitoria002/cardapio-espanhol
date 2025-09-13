<?php
require_once './db/conection.php';

$database = new Database();
$pdo = $database->getInstance();

echo "Categorias no banco:\n";
$stmt = $pdo->query("SELECT * FROM categoria ORDER BY id_categoria");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id_categoria'] . " - Nome: " . $row['nome_categoria'] . "\n";
}

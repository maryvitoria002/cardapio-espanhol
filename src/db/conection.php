<?php 

require_once realpath(__DIR__ . "/../define.php");

$dsn = "mysql:host=" . _SQL_HOST . ";dbname=" . _SQL_DATABASE;

try {
    $conn = new PDO($dsn, _SQL_USER, _SQL_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
    echo "Erro: " . $e->getMessage();
}
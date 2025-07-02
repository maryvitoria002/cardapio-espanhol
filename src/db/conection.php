<?php 

require_once realpath(__DIR__ . "/../define.php");

$dsn = "mysql:host=" . _SQL_HOST . ";dbname=" . _SQL_DATABASE;

try {
    $conn = new PDO($dsn, _SQL_USER, _SQL_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
    echo "Erro: " . $e->getMessage();
}

// require_once realpath(__DIR__ . "/../define.php");
// class Database{
    
//     public string $dsn = "mysql:host=" . _SQL_HOST . ";dbname=" . _SQL_DATABASE;
//     private static $conn;

//     public function getInstance() {
//         if(!isset(self::$conn)){
//             try {
//                 self::$conn = new PDO($this -> dsn, _SQL_USER, _SQL_PASS);
//                 self::$conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//             } catch (PDOException $e){
//                 echo "Erro: " . $e -> getMessage();
//             }
//         }

//         return self::$conn;
//     }

//     public function prepare($sql){
//         return self::getInstance()->prepare($sql);
//     }
// }
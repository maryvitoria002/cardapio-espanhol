<?php 

function insert($cpf, $nome, $nascimento, $email, $senha, $telefone, $preferencias, $nivel_acesso){
    require realpath(__DIR__ . "/../../db/conection.php");
    // $options = [
    //     'cost' => 11
    // ];
    // $senha = password_hash($senha, PASSWORD_BCRYPT, $options)."\n";
    if(isset($_POST["cadastrar"])){
        $sql = "INSERT INTO clientes VALUES (DEFAULT, :cpf, :nome, :nascimento, :email, :senha, :telefone, :preferencias, :nivel_acesso)";
        $stm = $conn->prepare($sql);
        $stm->bindParam(":cpf", $cpf, PDO::PARAM_INT);
        $stm->bindParam(":nome", $nome, PDO::PARAM_STR);
        $stm->bindParam(":nascimento", $nascimento, PDO::PARAM_STR);
        $stm->bindParam(":email", $email, PDO::PARAM_STR);
        $stm->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stm->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stm->bindParam(":preferencias", $preferencias, PDO::PARAM_STR);
        $stm->bindParam(":nivel_acesso", $nivel_acesso, PDO::PARAM_STR);

        try {
            $stm->execute();
            header("Location: ./index.php");
        } catch (PDOException $e){
            return $e->getMessage();
        }
    }
}

function login($email, $senha) {
    require realpath(__DIR__ . "/../../db/conection.php");
    // $options = [
    //     'cost' => 11
    // ];
    // $senha = password_hash($senha, PASSWORD_BCRYPT, $options)."\n";

    $sql = "SELECT * FROM clientes WHERE email = :email AND senha = :senha";
    $stm = $conn->prepare($sql);
    $stm->bindParam(":email", $email, PDO::PARAM_STR);
    $stm->bindParam(":senha", $senha, PDO::PARAM_STR);

    try {
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        if($result){
            session_start();
            $_SESSION["nome"] = $result["nome_completo"];
            $_SESSION["email"] = $result["email"];
            $_SESSION["nivel_acesso"] = $result["nivel_acesso"];
            $_SESSION["id"] = $result["cpf"];
            header("Location: ./index.php");
        } else {
            echo "<script>alert('Usuário ou senha inválidos')</script>";
        }
    } catch (PDOException $e){
        echo $e->getMessage();
    }
    
}
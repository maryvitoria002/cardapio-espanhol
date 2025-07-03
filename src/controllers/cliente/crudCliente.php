<?php 
require_once "Cliente.php";

class CrudCliente extends Cliente{
    
    public function insert(){
        $cpf = $this->getCpf();
        $nome_completo = $this->getNome();
        $nascimento = $this->getNat();
        $email = $this->getEmail();
        $senha = $this->getSenha();
        $telefone = $this->getTelefone();
        $nivel_acesso = $this->getNivelAcesso();
        $sql = "INSERT INTO `{$this->tabela}` (cpf, nome_completo, nascimento, email, senha, telefone, nivel_acesso) VALUES (:cpf, :nome_completo, :nascimento, :email, :senha, :telefone, :nivel_acesso)";
        $stmt = Database::prepare($sql);
        $stmt->bindParam(":cpf", $cpf, PDO::PARAM_STR);
        $stmt->bindParam(":nome_completo", $nome_completo, PDO::PARAM_STR);
        $stmt->bindParam(":nascimento", $nascimento, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        $stmt->bindParam(":nivel_acesso", $nivel_acesso, PDO::PARAM_STR);
 
        try {
            $stmt->execute();
            header("Location: ./login.php");
        } catch (PDOException $e) {
            echo "<script>alert('Erro: " . $e->getMessage() . "')</script>";
        }
        
    }

    public function login() {
        $email = $this->getEmail();
        $senha = $this->getSenha();
        $sql = "SELECT * FROM `{$this->tabela}` WHERE email = :email AND senha = :senha";
        $stmt = Database::prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
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
}



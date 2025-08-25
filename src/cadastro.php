<!DOCTYPE html>
<?php 
require_once "./controllers/cliente/crudCliente.php";
$cliente = new CrudCliente();

if(isset($_POST["cadastrar"])){
    if(empty($_POST["nome1"]) || empty($_POST["nome2"]) || empty($_POST["email"]) || empty($_POST["senha"]) || empty($_POST["telefone"])) {
        echo "<script>alert('Todos os campos são obrigatórios!');</script>";
    } else {
        $primeiro_nome = $_POST["nome1"];
        $segun_nome = $_POST["nome2"];
        $email = $_POST["email"];
        $senha = md5($_POST["senha"]);
        $telefone = $_POST["telefone"];

        try {
            $cliente->setNome1($primeiro_nome);
            $cliente->setNome2($segun_nome);
            $cliente->setEmail($email);
            $cliente->setSenha($senha);
            $cliente->setTelefone($telefone);
            $cliente->setData();
            $cliente->setImagem(NULL);
            $cliente->insert();
        } catch (Exception $e) {
            echo "<script>alert('Erro no cadastro: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}
?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/style.css">
    <title>Écoute Saveur | Cadastro</title>
</head>
<body>
    <div class="container">
        <div class="esquerda">
            <h1>
                <strong>Écoute</strong> <span>Saveur</span>
            </h1>
            <p id="login">Faça cadastro:</p>
            
            <form action="./cadastro.php" method="POST">
                <div style="display: flex; gap: 10px">
                    <div style="width: 100%;">
                        <label for="nome1">Primeiro nome</label>
                        <input name="nome1" type="text" placeholder="Digite aqui..." />
                    </div>
                    
                    <div style="width: 100%;">
                        <label for="nome2">Segundo nome</label>
                        <input name="nome2" type="text" placeholder="Digite aqui..." />
                    </div>
                </div>
                
        
                <label for="email">Email</label>
                <input name="email" type="email" placeholder="Digite aqui..." />

                <label for="senha">Senha</label>
                <input name="senha" type="password" placeholder="Digite aqui..."   />

                <div style="display: flex; gap: 10px">
                    
                    <div style="width: 100%;">
                        <label for="telefone">Telefone</label>
                        <input name="telefone" type="text" placeholder="Digite aqui..." />
                    </div>
                </div>
                
                <div class="links">
                    <a href="#" style="color: grey" >Esqueceu a senha?</a>
                    <p><strong>Já possui uma conta?</strong><a href="./login.php"><span style="color: #f1c40f">Fazer login</span></a></p>
                </div>
                <button id="entrar" name="cadastrar">CADASTRAR</button>
            </form>

            <div class="termo">
                Termos de serviço | Política de Privacidade
            </div>
        </div>
        
        <div class="direita">
            <div class="logos">
                <img src="https://img.icons8.com/fluency/48/restaurant.png" />
                <img src="https://img.icons8.com/fluency/48/chef-hat.png" />
            </div>
        </div>
  </div>
</body>
</html>
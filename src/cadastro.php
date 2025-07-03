<!DOCTYPE html>
<?php 
require_once "./controllers/cliente/CrudCliente.php";
$cliente = new CrudCliente();

if(isset($_POST["cadastrar"])){
    $nome_completo = $_POST["nome"];
    $nascimento = $_POST["nascimento"];
    $email = $_POST["email"];
    $senha = md5($_POST["senha"]);
    $cpf = $_POST["cpf"];
    $telefone = $_POST["telefone"];

    $cliente->setNome($nome_completo);
    $cliente->setNat($nascimento);
    $cliente->setEmail($email);
    $cliente->setSenha($senha);
    $cliente->setCpf($cpf);
    $cliente->setTelefone($telefone);
    $cliente->setNivelAcesso("Comum");
    $cliente->insert();
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
                        <label for="nome">Nome Completo</label>
                        <input name="nome" type="text" placeholder="Digite aqui..." />
                    </div>
                    
                    <div style="width: 100%;">
                        <label for="nascimento">Nascimento</label>
                        <input name="nascimento" type="date" placeholder="01/01/0001" />
                    </div>
                </div>
                
        
                <label for="email">Email</label>
                <input name="email" type="email" placeholder="Digite aqui..." />

                <label for="senha">Senha</label>
                <input name="senha" type="senha" placeholder="Digite aqui..."   />

                <div style="display: flex; gap: 10px">
                    <div style="width: 100%;">
                        <label for="cpf">CPF</label>
                        <input name="cpf" type="text" placeholder="Digite aqui..." />
                    </div>
                    
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
<!DOCTYPE html>
<?php 
require_once "./controllers/cliente/crudCliente.php";

if(isset($_POST["entrar"])){
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    login($email, $senha);
}

?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/style.css">
    <title>Écoute Saveur | Página de login</title>
</head>
<body>
    <div class="container">
        <div class="esquerda">
            <h1>
                <strong>Écoute</strong> <span>Saveur</span>
            </h1>
            <p id="login">Faça Login:</p>

            <div class="social-button facebook">
                <img src="https://img.icons8.com/ios-filled/20/ffffff/facebook-new.png" /> Facebook
            </div>
            
            <div class="social-button google">
                <img src="https://img.icons8.com/color/20/google-logo.png" /> Google
            </div>

            <div class="divide">Ou</div>
            
            <form action="./login.php" method="POST">
                <label for="email">Email ou Nome de Usuário</label>
                <input name="email" type="email" placeholder="Digite aqui..." />
               
                <label for="senha">Senha</label>
                <input name="senha" type="senha" placeholder="Digite aqui..."   />
                
                <div class="links">
                    <a href="#" style="color: grey" >Esqueceu a senha?</a>
                    <p><strong>Ainda não tem uma conta?</strong><a href="#"><span style="color: #f1c40f">Cadastre-se</span></a></p>
                </div>
                <button id="entrar" name="entrar">ENTRAR</button>
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
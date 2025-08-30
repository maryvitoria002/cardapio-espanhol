<!DOCTYPE html>
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
                 
            <form action="./forms/cadastro_form.php" method="POST">
                <div style="display: flex; gap: 10px">
                    <div style="width: 100%;">
                        <label for="primeiro_nome">Primeiro nome</label>
                        <input name="primeiro_nome" type="text" placeholder="Digite aqui..." />
                    </div>
                    
                    <div style="width: 100%;">
                        <label for="segundo_nome">Segundo nome</label>
                        <input name="segundo_nome" type="text" placeholder="Digite aqui..." />
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
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/style.css">
    <title>Écoute Saveur | <?= $titulo ?></title>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
        <!-- 
            <nav>
                <ul>
                    <li>
                        <img src="./assets/logo.png" alt="Logo">
                    </li>
                    <li><a href="./index.php">
                        <img src="./assets/inicio.png" alt="Menu">
                    </a></li>
                    <li><a href="./cardapio.php">
                            <img src="./assets/cardapio.png" alt="Cardápio">
                    </a></li>
                    <li><a href="./carrinho.php">
                        <img src="./assets/carrinho.png" alt="Carrinho">
                    </a></li>
                    <li><a href="./historico.php">
                        <img src="./assets/historico.png" alt="Histórico">
                    </a></li>
                </ul>
                <ul id="configuracoes">
                    <li><a href="./configuracoes.php">
                        <img src="./assets/configuracoes.png" alt="Configurações">
                    </a></li>
                </ul>
            </nav>
        </div> -->

            <!-- Sidebar Header -->
            <header class="sidebar-header">
                <a href="#" class="header-logo">
                    <span>E.S</span>
                </a>
                <button class="sidebar-toggler">
                    <!-- <i class="fa-solid fa-chevron-right"></i> -->
                     <!-- Traçinho pra diminuir barra lateral -->
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            </header>

            
            <nav class="sidebar-nav">

                <!-- Top nav primária -->
                <ul class="nav-list primary-nav">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <img src="./assets/inicio.png" alt="inicio">
                            <span class="nav-label">Início</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <img src="./assets/cardapio.png" alt="cardapio">
                            <span class="nav-label">Cardápio</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <img src="./assets/carrinho.png" alt="carrinho">
                            <span class="nav-label">Carrinho</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <img src="./assets/historico.png" alt="historico">
                            <span class="nav-label">Histórico</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <img src="./assets/configuracoes.png" alt="configuracoes">
                            <span class="nav-label">Configurações</span>
                        </a>
                    </li>
                </ul>

                <!-- Top nav secundária -->
                <ul class="nav-list secondary-nav">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <img src="./assets/sair.png" alt="sair">
                            <span class="nav-label">Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="esquerda">…</div>
        <div class="direita"></div>
    </div>

    <!-- Script -->
    <script src="https://kit.fontawesome.com/9c2b5a2876.js" crossorigin="anonymous"></script>
    <script src="./js/script.js"></script>
</body>
</html>

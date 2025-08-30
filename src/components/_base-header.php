<!DOCTYPE html>
<?php 
session_start();

// if(!isset($_SESSION["nome"])){
//     header("Location: ./login.php");
//}

?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/<?=$titulo?>.css">
    <title>Écoute Saveur | <?= $titulo ?></title>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
        
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
                        <a href="./index.php" class="nav-link">
                            <img src="./assets/inicio.png" alt="inicio">
                            <span class="nav-label">Início</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./cardapio.php" class="nav-link">
                            <img src="./assets/cardapio.png" alt="cardapio">
                            <span class="nav-label">Cardápio</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./carrinho.php" class="nav-link">
                            <img src="./assets/carrinho.png" alt="carrinho">
                            <span class="nav-label">Carrinho</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./historico.php" class="nav-link">
                            <img src="./assets/historico.png" alt="historico">
                            <span class="nav-label">Histórico</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./configuracoes.php" class="nav-link">
                            <img src="./assets/configuracoes.png" alt="configuracoes">
                            <span class="nav-label">Configurações</span>
                        </a>
                    </li>
                </ul>

                <!-- Top nav secundária -->
                <ul class="nav-list secondary-nav">
                    <li class="nav-item">
                        <a href="./sair.php" class="nav-link">
                            <img src="./assets/sair.png" alt="sair">
                            <span class="nav-label">Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- conteudo da pagina -->



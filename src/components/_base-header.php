<!DOCTYPE html>
<?php 
// session_start(); // Removido - deve ser chamado antes de incluir este arquivo

// if(!isset($_SESSION["nome"])){
//     header("Location: ./login.php");
//}

?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="./assets/favicon.jpg">
    <link rel="shortcut icon" type="image/jpeg" href="./assets/favicon.jpg">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/responsive.css">
    <link rel="stylesheet" href="./styles/<?=$titulo?>.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Identity Services -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <title>Écoute Saveur | <?= $titulo ?></title>
</head>
<body>
    <div class="container">
        <!-- Botão menu mobile -->
        <button class="sidebar-menu-button" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>
        
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
                            <span class="nav-label">Inicio</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./cardapio.php" class="nav-link">
                            <img src="./assets/cardapio.png" alt="cardapio">
                            <span class="nav-label">Menú</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./carrinho.php" class="nav-link">
                            <img src="./assets/carrinho.png" alt="carrinho">
                            <span class="nav-label">Carrito</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./historico.php" class="nav-link">
                            <img src="./assets/historico.png" alt="historico">
                            <span class="nav-label">Historial</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./configuracoes.php" class="nav-link">
                            <img src="./assets/configuracoes.png" alt="configuracoes">
                            <span class="nav-label">Configuraciones</span>
                        </a>
                    </li>
                </ul>

                <!-- Top nav secundária -->
                <ul class="nav-list secondary-nav">
                    <li class="nav-item">
                        <a href="./sair.php" class="nav-link">
                            <img src="./assets/sair.png" alt="sair">
                            <span class="nav-label">Salir</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- conteudo da pagina -->

        <script>
        // JavaScript para menu mobile responsivo
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarMenuBtn = document.querySelector('.sidebar-menu-button');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebarMenuBtn && sidebar) {
                sidebarMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('collapsed');
                });
                
                // Fechar menu ao clicar fora
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768 && 
                        !sidebar.contains(e.target) && 
                        !sidebarMenuBtn.contains(e.target) && 
                        sidebar.classList.contains('collapsed')) {
                        sidebar.classList.remove('collapsed');
                    }
                });
                
                // Fechar menu ao clicar no overlay
                sidebar.addEventListener('click', function(e) {
                    if (e.target === sidebar) {
                        sidebar.classList.remove('collapsed');
                    }
                });
                
                // Ajustar menu ao redimensionar tela
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        sidebar.classList.remove('collapsed');
                    }
                });
            }
        });
        </script>



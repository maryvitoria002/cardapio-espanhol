<div class="header">
    <div class="header-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h2 class="page-title">
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            $page_titles = [
                'index.php' => 'Dashboard',
                'usuarios.php' => 'Usuários',
                'funcionarios.php' => 'Funcionários', 
                'categorias.php' => 'Categorias',
                'produtos.php' => 'Produtos',
                'pedidos.php' => 'Pedidos',
                'avaliacoes.php' => 'Avaliações'
            ];
            
            echo $page_titles[$current_page] ?? 'Admin';
            ?>
        </h2>
    </div>
    
    <div class="header-right">
        <div class="header-actions">
            <button class="header-btn" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </button>
            
            <div class="user-menu">
                <button class="user-btn" onclick="toggleUserMenu()">
                    <img src="<?= !empty($_SESSION['admin_imagem']) ? '../images/' . $_SESSION['admin_imagem'] : '../assets/avatar.png' ?>" alt="Perfil" class="user-avatar">
                    <span class="user-name"><?= $_SESSION['admin_nome'] ?? 'Admin' ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                
                <div class="user-dropdown" id="userDropdown">
                    <a href="perfil.php">
                        <i class="fas fa-user"></i>
                        Meu Perfil
                    </a>
                    <a href="configuracoes.php">
                        <i class="fas fa-cog"></i>
                        Configurações
                    </a>
                    <hr>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

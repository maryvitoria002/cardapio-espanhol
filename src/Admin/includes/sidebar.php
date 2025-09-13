<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-utensils"></i>
            <span>RestauranteSIS</span>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <ul>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['REQUEST_URI'], '/Admin/') !== false ? 'active' : '' ?>">
                <a href="/Ecoute-Saveur---projeto-de-restaurante/src/Admin/">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="menu-section">
                <span>GESTÃO</span>
            </li>
            
            <li class="<?= strpos($_SERVER['REQUEST_URI'], 'usuarios') !== false ? 'active' : '' ?>">
                <a href="/Ecoute-Saveur---projeto-de-restaurante/src/Admin/usuarios/">
                    <i class="fas fa-users"></i>
                    <span>Usuários</span>
                </a>
            </li>
            
            <li class="<?= strpos($_SERVER['REQUEST_URI'], 'funcionarios') !== false ? 'active' : '' ?>">
                <a href="/Ecoute-Saveur---projeto-de-restaurante/src/Admin/funcionarios/">
                    <i class="fas fa-user-tie"></i>
                    <span>Funcionários</span>
                </a>
            </li>
            
            <li class="<?= strpos($_SERVER['REQUEST_URI'], 'categorias') !== false ? 'active' : '' ?>">
                <a href="/Ecoute-Saveur---projeto-de-restaurante/src/Admin/categorias/">
                    <i class="fas fa-tags"></i>
                    <span>Categorias</span>
                </a>
            </li>
            
            <li class="<?= strpos($_SERVER['REQUEST_URI'], 'produtos') !== false ? 'active' : '' ?>">
                <a href="/Ecoute-Saveur---projeto-de-restaurante/src/Admin/produtos/">
                    <i class="fas fa-box"></i>
                    <span>Produtos</span>
                </a>
            </li>
            
            <li class="<?= strpos($_SERVER['REQUEST_URI'], 'pedidos') !== false ? 'active' : '' ?>">
                <a href="/Ecoute-Saveur---projeto-de-restaurante/src/Admin/pedidos/">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pedidos</span>
                </a>
            </li>
            
            <li class="<?= strpos($_SERVER['REQUEST_URI'], 'avaliacoes') !== false ? 'active' : '' ?>">
                <a href="/Ecoute-Saveur---projeto-de-restaurante/src/Admin/avaliacoes/">
                    <i class="fas fa-star"></i>
                    <span>Avaliações</span>
                </a>
            </li>
            
            <li class="menu-section">
                <span>SISTEMA</span>
            </li>
            
            <li>
                <a href="<?= strpos($_SERVER['REQUEST_URI'], '/Admin/') !== false ? 'logout.php' : '../logout.php' ?>">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </li>
        </ul>
    </div>
</div>

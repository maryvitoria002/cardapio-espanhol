<?php 
$titulo = "inicio";
include_once "./components/_base-header.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION["id"])) {
    header("Location: ./login.php");
    exit();
}

?>
    <div class="container">
        <!-- Coluna principal (esquerda) -->
        <main class="main-content">
            <!-- Cabeçalho -->
            <header class="header">
                <div class="greeting">
                    <h1>Olá, <?= $_SESSION["primeiro_nome"]?></h1>
                </div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="O que você quer comer hoje...Kelvin?Eu também!">
                </div>
                <div class="top-actions">
                    <button class="icon-btn">
                        <i class="fas fa-comment"></i>
                    </button>
                    <button class="icon-btn">
                        <i class="fas fa-star"></i>
                    </button>
                    <div class="avatar">
                        <img src="./images/usuário.jpeg" alt="Usuário">
                        <span class="status"></span>
                    </div>
                </div>
            </header>

            <!-- Banner principal -->
            <section class="banner">
                <div class="banner-content">
                    <h2>OUÇA O SABOR</h2>
                    <h3>VIVA O <span class="highlight">ENCANTO</span></h3>
                </div>
                <div class="banner-image">
                    <img src="assets/Logo.png" alt="Pratos diversos">
                </div>
            </section>

            <!-- Categorias -->
            <section class="categories">
                <div class="section-header">
                    <h2>CATEGORIAS</h2>
                    <button class="see-all">Ver tudo...</button>
                </div>
                <div class="category-list">
                    <div class="category-item">
                        <div class="category-icon">
                            <img src="assets/bebidas.png" alt="Bebidas">
                        </div>
                        <span>Bebidas</span>
                    </div>
                    <div class="category-item active">
                        <div class="category-icon">
                            
                            <img src="assets/bowls.png" alt="Bowls">
                        </div>
                        <span>Bowls</span>
                    </div>
                    <div class="category-item">
                        <div class="category-icon">
                            
                            <img src="./images/usuário.jpeg" alt="Lanches">
                        </div>
                        <span>Lanches</span>
                    </div>
                    <div class="category-item">
                        <div class="category-icon">
                            
                            <img src="assets/sobremesa.png" alt="Sobremesa">
                        </div>
                        <span>Sobremesa</span>
                    </div>
                    <div class="category-item">
                        <div class="category-icon">
                            
                            <img src="assets/carnes.png" alt="Carnes">
                        </div>
                        <span>Carnes</span>
                    </div>
                </div>
            </section>

            <!-- Produtos -->
            <section class="products">
                <div class="section-header">
                    <h2>MAIS PEDIDOS</h2>
                    <button class="see-all">Ver tudo...</button>
                </div>
                <div class="product-list">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/saladaceasar.png" alt="Salada Caesar">
                        </div>
                        <h3 class="product-name">Salada Caesar</h3>
                        <p class="product-price">R$ 9,99</p>
                        <div class="product-meta">
                            <span>22Min</span>
                            <span>4,47km</span>
                        </div>
                        <button class="add-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/gaspachoverde.png" alt="Gaspacho Verde">
                        </div>
                        <h3 class="product-name">Gaspacho Verde</h3>
                        <p class="product-price">R$ 9,99</p>
                        <div class="product-meta">
                            <span>22Min</span>
                            <span>4,47km</span>
                        </div>
                        <button class="add-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/Ramentori.png" alt="Ramen Tori Tamago">
                        </div>
                        <h3 class="product-name">Ramen Tori Tamago</h3>
                        <p class="product-price">R$ 9,99</p>
                        <div class="product-meta">
                            <span>22Min</span>
                            <span>4,47km</span>
                        </div>
                        <button class="add-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </section>
        </main>

       
        <aside class="sidebar">
            <!-- Card de endereço -->
            <div class="address-card">
                <h2>Seu endereço</h2>
                <div class="address-line">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Rua Manoel Vitorino, 10</span>
                </div>
                <p class="address-placeholder">
                    Apartamento 305<br>
                    Acima do real móveis<br>
                    Referência: Casa de dona dedé
                </p>
                <div class="address-actions">
                    <button class="btn-solid">Alterar</button>
                    <button class="btn-outline">Add Detalhes</button>
                    <button class="btn-outline">Add Nota</button>
                </div>
            </div>

            <div class="order-card">
                <h2>Menu de Pedidos</h2>
                <div class="order-list">
                    <div class="order-item">
                        <div class="order-thumb">
                            <img src="assets/Ramentori.png" alt="Ramen Tori Tamago">
                        </div>
                        <div class="order-details">
                            <h3 class="order-name">Ramen Tori Tamago</h3>
                            <span class="order-price">+R$ 9,99</span>
                            <span class="order-qty">1x</span>
                        </div>
                    </div>
                    <div class="order-item">
                        <div class="order-thumb">
                            <img src="assets/gaspachoverde.png" alt="Gaspacho Verde">
                        </div>
                        <div class="order-details">
                            <h3 class="order-name">Gaspacho Verde</h3>
                            <span class="order-price">+R$ 9,99</span>
                            <span class="order-qty">1x</span>
                        </div>
                    </div>
                    <div class="order-fee">
                        <span>Serviços</span>
                        <span class="order-price">+R$ 1,00</span>
                    </div>
                    <div class="order-total">
                        <span>TOTAL</span>
                        <span>R$ 20,98</span>
                    </div>
                </div>
            </div>

            

    <script>
        // java script
        document.querySelectorAll('.add-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.add('clicked');
                setTimeout(() => {
                    this.classList.remove('clicked');
                }, 500);
            });
        });

        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelector('.category-item.active').classList.remove('active');
                this.classList.add('active');
            });
        });
    </script>




<?php 
include_once "./components/_base-footer.php";
?>
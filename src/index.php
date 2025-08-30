<?php 
$titulo = "inicio";
include_once "./components/_base-header.php";
?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/inicio.css">
</head>
<body>
    <div class="container">
        <!-- Coluna principal (esquerda) -->
        <main class="main-content">
            <!-- Cabeçalho -->
            <header class="header">
                <div class="greeting">
                    <h1>Olá, Mary!</h1>
                </div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="O que você quer comer hoje...">
                </div>
                <div class="top-actions">
                    <button class="icon-btn">
                        <i class="fas fa-comment"></i>
                    </button>
                    <button class="icon-btn">
                        <i class="fas fa-star"></i>
                    </button>
                    <div class="avatar">
                        <img src="" alt="Usuário">
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
                    <img src="https://i.imgur.com/zX0lPkr.png" alt="Pratos diversos">
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
                            
                            <img src="assets/bebida.png" alt="Bowls">
                        </div>
                        <span>Bowls</span>
                    </div>
                    <div class="category-item">
                        <div class="category-icon">
                            
                            <img src="assets/lanches.png" alt="Lanches">
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
                            <img src="https://i.imgur.com/5r3Tj6f.jpg" alt="Salada Caesar">
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
                            <img src="https://i.imgur.com/9xY71qj.jpg" alt="Gaspacho Verde">
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
                            <img src="https://i.imgur.com/VLbXa0h.jpg" alt="Ramen Tori Tamago">
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
                    <span>Rua Joaquim Manuel, 10</span>
                </div>
                <p class="address-placeholder">
                    Apartamento 102, Bloco B<br>
                    Próximo ao mercado<br>
                    Referência: prédio azul
                </p>
                <div class="address-actions">
                    <button class="btn-solid">Alterar</button>
                    <button class="btn-outline">Add Detalhes</button>
                    <button class="btn-outline">Add Nota</button>
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
</body>
</html>

<?php 
include_once "./components/_base-footer.php";
?>
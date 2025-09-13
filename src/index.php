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

// Incluir controladores necessários
require_once './controllers/produto/Crud_produto.php';
require_once './controllers/categoria/Crud_categoria.php';

// Criar instâncias para buscar dados
$crudProduto = new Crud_produto();
$crudCategoria = new Crud_categoria();

// Buscar produtos em destaque (limitando a 6 para não sobrecarregar a página)
$produtos = $crudProduto->read();
$produtosDestaque = array_slice($produtos, 0, 6); // Pegar apenas os 6 primeiros

// Buscar categorias
$categorias = $crudCategoria->read();

?>
<link rel="stylesheet" href="./styles/inicio_produtos.css">
    <div class="container">
        <!-- Coluna principal (esquerda) -->
        <main class="main-content">
            <!-- Cabeçalho -->
            <header class="header">
                <div class="greeting">
                    <h1>Olá, <?= isset($_SESSION["primeiro_nome"]) ? $_SESSION["primeiro_nome"] : "Usuário" ?></h1>
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
                    <a href="cardapio.php" class="see-all">Ver tudo...</a>
                </div>
                <div class="product-list">
                    <?php if (!empty($produtosDestaque)): ?>
                        <?php foreach ($produtosDestaque as $produto): ?>
                            <div class="product-card">
                                <a href="produto.php?id=<?php echo $produto['id_produto']; ?>" class="product-link">
                                    <div class="product-image">
                                        <?php if (!empty($produto['imagem'])): ?>
                                            <img src="./images/comidas/<?php echo htmlspecialchars($produto['imagem']); ?>" 
                                                 alt="<?php echo htmlspecialchars($produto['nome_produto']); ?>">
                                        <?php else: ?>
                                            <img src="./assets/avatar.png" alt="Produto sem imagem">
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="product-name"><?php echo htmlspecialchars($produto['nome_produto']); ?></h3>
                                    <p class="product-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                    <div class="product-meta">
                                        <span>Disponível</span>
                                        <span>Estoque: <?php echo $produto['estoque']; ?></span>
                                    </div>
                                </a>
                                <form method="POST" action="carrinho.php" style="display: inline;" onclick="event.stopPropagation();">
                                    <input type="hidden" name="acao" value="adicionar">
                                    <input type="hidden" name="id_produto" value="<?php echo $produto['id_produto']; ?>">
                                    <input type="hidden" name="quantidade" value="1">
                                    <button type="submit" class="add-btn" 
                                            <?php echo $produto['estoque'] <= 0 ? 'disabled' : ''; ?>
                                            onclick="event.stopPropagation();">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-products">
                            <p>Nenhum produto disponível no momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
            
    <script>
        // Feedback visual para botões de adicionar ao carrinho
        document.querySelectorAll('.add-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Prevenir que o clique no botão ative o link do produto
                e.stopPropagation();
                e.preventDefault();
                
                // Adicionar classe de feedback visual
                this.classList.add('clicked');
                
                // Criar elemento de feedback
                const feedback = document.createElement('div');
                feedback.textContent = 'Adicionado!';
                feedback.style.position = 'absolute';
                feedback.style.top = '-30px';
                feedback.style.left = '50%';
                feedback.style.transform = 'translateX(-50%)';
                feedback.style.background = '#28a745';
                feedback.style.color = 'white';
                feedback.style.padding = '5px 10px';
                feedback.style.borderRadius = '4px';
                feedback.style.fontSize = '12px';
                feedback.style.opacity = '0';
                feedback.style.transition = 'opacity 0.3s';
                feedback.style.zIndex = '1000';
                
                // Adicionar posição relativa ao card se não tiver
                const card = this.closest('.product-card');
                card.style.position = 'relative';
                card.appendChild(feedback);
                
                // Mostrar feedback
                setTimeout(() => {
                    feedback.style.opacity = '1';
                }, 10);
                
                // Submeter o formulário após o feedback
                setTimeout(() => {
                    this.closest('form').submit();
                }, 800);
                
                // Remover feedback após 2 segundos
                setTimeout(() => {
                    feedback.style.opacity = '0';
                    setTimeout(() => {
                        if (feedback.parentNode) {
                            feedback.parentNode.removeChild(feedback);
                        }
                    }, 300);
                }, 2000);
                
                // Remover classe de clique após animação
                setTimeout(() => {
                    this.classList.remove('clicked');
                }, 500);
            });
        });

        // Navegação por categorias
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remover classe ativa de todos os itens
                document.querySelectorAll('.category-item').forEach(cat => {
                    cat.classList.remove('active');
                });
                
                // Adicionar classe ativa ao item clicado
                this.classList.add('active');
            });
        });

        // Melhorar a interação dos cards de produto
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function(e) {
                // Se não foi clicado no botão, ir para a página do produto
                if (!e.target.closest('.add-btn') && !e.target.closest('form')) {
                    const link = this.querySelector('.product-link');
                    if (link) {
                        window.location.href = link.href;
                    }
                }
            });
        });
    </script>




<?php 
include_once "./components/_base-footer.php";
?>
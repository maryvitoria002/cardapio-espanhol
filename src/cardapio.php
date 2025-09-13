<?php 
$titulo = "cardapio";
include_once "./components/_base-header.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION["id"])) {
    header("Location: ./login.php");
    exit();
}

// Incluir as classes necessárias
require_once __DIR__ . '/db/conection.php';
require_once __DIR__ . '/controllers/produto/Crud_produto.php';
require_once __DIR__ . '/controllers/categoria/Crud_categoria.php';

// Inicializar variáveis
$produtos = [];
$categorias = [];
$categoria_selecionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$categoria_id_selecionada = isset($_GET['categoria_id']) ? $_GET['categoria_id'] : '';
$termo_busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

try {
    // Criar conexão com o banco
    $database = new Database();
    $conexao = $database->getInstance();
    
    // Buscar categorias
    $stmt = $conexao->prepare("SELECT * FROM categoria ORDER BY nome_categoria");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar produtos
    $sql = "SELECT p.*, c.nome_categoria as categoria_nome 
            FROM produto p 
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria 
            WHERE p.status = 'Disponivel'";
    
    $params = [];
    
    // Filtrar por categoria se selecionada
    if (!empty($categoria_selecionada)) {
        $sql .= " AND c.nome_categoria = :categoria";
        $params['categoria'] = $categoria_selecionada;
    } elseif (!empty($categoria_id_selecionada)) {
        $sql .= " AND c.id_categoria = :categoria_id";
        $params['categoria_id'] = $categoria_id_selecionada;
    }
    
    // Filtrar por termo de busca
    if (!empty($termo_busca)) {
        $sql .= " AND (p.nome_produto LIKE :busca OR p.descricao LIKE :busca)";
        $params['busca'] = "%$termo_busca%";
    }
    
    $sql .= " ORDER BY c.nome_categoria, p.nome_produto";
    
    $stmt = $conexao->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar produtos por categoria
    $produtos_por_categoria = [];
    foreach ($produtos as $produto) {
        $cat = $produto['categoria_nome'] ?? 'Sem Categoria';
        if (!isset($produtos_por_categoria[$cat])) {
            $produtos_por_categoria[$cat] = [];
        }
        $produtos_por_categoria[$cat][] = $produto;
    }
    
} catch (Exception $e) {
    $erro = "Erro ao carregar produtos: " . $e->getMessage();
}
?>

<div class="container">
    <main class="main-content">
        <!-- Cabeçalho -->
        <header class="header">
            <div class="greeting">
                <h1>Nosso Cardápio</h1>
                <p>Descubra os sabores únicos do Écoute Saveur</p>
            </div>
            <div class="search-bar">
                <form method="GET" class="search-form">
                    <i class="fas fa-search"></i>
                    <input type="text" name="busca" placeholder="Buscar pratos..." value="<?= htmlspecialchars($termo_busca) ?>">
                    <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria_selecionada) ?>">
                </form>
            </div>
            <div class="top-actions">
                <button class="icon-btn cart-btn" onclick="window.location.href='./carrinho.php'">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </button>
                <div class="avatar">
                    <img src="./images/usuário.jpeg" alt="Usuário">
                    <span class="status"></span>
                </div>
            </div>
        </header>

        <?php if (isset($erro)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($erro) ?>
        </div>
        <?php endif; ?>

        <!-- Filtros de Categoria -->
        <section class="categories-filter">
            <div class="section-header">
                <h2>CATEGORIAS</h2>
                <button class="clear-filter" onclick="limparFiltros()">Limpar Filtros</button>
            </div>
            <div class="category-list">
                <div class="category-item <?= empty($categoria_selecionada) ? 'active' : '' ?>" 
                     data-categoria=""
                     onclick="filtrarCategoria('')">
                    <div class="category-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <span>Todos</span>
                </div>
                <?php foreach ($categorias as $categoria): ?>
                <div class="category-item <?= $categoria_selecionada === $categoria['nome_categoria'] ? 'active' : '' ?>" 
                     data-categoria="<?= htmlspecialchars($categoria['nome_categoria']) ?>"
                     onclick="filtrarCategoria('<?= htmlspecialchars($categoria['nome_categoria']) ?>')">
                    <div class="category-icon">
                        <?php if (isset($categoria['imagem']) && $categoria['imagem']): ?>
                            <img src="<?= htmlspecialchars($categoria['imagem']) ?>" alt="<?= htmlspecialchars($categoria['nome_categoria']) ?>">
                        <?php else: ?>
                            <i class="fas fa-utensils"></i>
                        <?php endif; ?>
                    </div>
                    <span><?= htmlspecialchars($categoria['nome_categoria']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Produtos por Categoria -->
        <?php if (!empty($produtos_por_categoria)): ?>
            <?php foreach ($produtos_por_categoria as $categoria_nome => $produtos_categoria): ?>
            <section class="products-section">
                <div class="section-header">
                    <h2><?= htmlspecialchars($categoria_nome) ?></h2>
                    <span class="item-count"><?= count($produtos_categoria) ?> itens</span>
                </div>
                <div class="product-grid">
                    <?php foreach ($produtos_categoria as $produto): ?>
                    <div class="product-card" data-categoria="<?= htmlspecialchars($categoria_nome) ?>">
                        <a href="./produto.php?id=<?= $produto['id_produto'] ?>" class="product-link">
                            <div class="product-image">
                                <?php if ($produto['imagem']): ?>
                                    <img src="./images/comidas/<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                                <?php else: ?>
                                    <img src="./assets/cardapio.png" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                                <?php endif; ?>
                                <div class="product-badges">
                                    <?php if ($produto['preco'] < 15): ?>
                                    <span class="badge badge-promo">Promoção</span>
                                    <?php endif; ?>
                                    <?php if (rand(0, 1)): ?>
                                    <span class="badge badge-popular">Popular</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?= htmlspecialchars($produto['nome_produto']) ?></h3>
                                <p class="product-description"><?= htmlspecialchars($produto['descricao']) ?></p>
                                <div class="product-meta">
                                    <div class="product-rating">
                                        <div class="stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= 4 ? 'filled' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="rating-count">(<?= rand(10, 50) ?>)</span>
                                    </div>
                                    <div class="product-time">
                                        <i class="fas fa-clock"></i>
                                        <span><?= rand(15, 30) ?>min</span>
                                    </div>
                                </div>
                                <div class="product-footer">
                                    <div class="product-price">
                                        R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="product-actions-external">
                            <button class="qty-btn minus" onclick="diminuirQuantidade(<?= $produto['id_produto'] ?>)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="qty-display" id="qty-<?= $produto['id_produto'] ?>">0</span>
                            <button class="qty-btn plus" onclick="aumentarQuantidade(<?= $produto['id_produto'] ?>)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-utensils fa-3x"></i>
            <h3>Nenhum produto encontrado</h3>
            <p><?php if ($termo_busca): ?>
                Não encontramos produtos para "<?= htmlspecialchars($termo_busca) ?>"
            <?php elseif ($categoria_selecionada): ?>
                Não há produtos disponíveis na categoria "<?= htmlspecialchars($categoria_selecionada) ?>"
            <?php else: ?>
                Nosso cardápio está sendo atualizado. Volte em breve!
            <?php endif; ?></p>
        </div>
        <?php endif; ?>
    </main>
    
    <!-- Carrinho flutuante -->
    <div class="floating-cart" id="floatingCart" style="display: none;">
        <div class="cart-header">
            <h3>Seu Pedido</h3>
            <button class="close-cart" onclick="fecharCarrinho()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Items serão adicionados via JavaScript -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <strong>Total: R$ <span id="cartTotal">0,00</span></strong>
            </div>
            <button class="checkout-btn" onclick="finalizarPedido()">
                <i class="fas fa-check"></i>
                Finalizar Pedido
            </button>
        </div>
    </div>
</div>

<script>
    // Variáveis globais
    let carrinho = {};
    let totalCarrinho = 0;

    // Filtrar por categoria
    function filtrarCategoria(categoria) {
        console.log('Filtrando por categoria:', categoria);
        const url = new URL(window.location);
        if (categoria) {
            url.searchParams.set('categoria', categoria);
        } else {
            url.searchParams.delete('categoria');
        }
        console.log('Nova URL:', url.toString());
        window.location = url;
    }

    // Limpar filtros
    function limparFiltros() {
        console.log('Limpando filtros');
        window.location = window.location.pathname;
    }

    // Controle de quantidade
    function aumentarQuantidade(idProduto) {
        if (!carrinho[idProduto]) {
            carrinho[idProduto] = 0;
        }
        carrinho[idProduto]++;
        atualizarDisplay(idProduto);
        atualizarCarrinho();
        
        // Enviar para o servidor
        adicionarAoCarrinhoServidor(idProduto, 1);
    }

    function diminuirQuantidade(idProduto) {
        if (carrinho[idProduto] && carrinho[idProduto] > 0) {
            carrinho[idProduto]--;
            if (carrinho[idProduto] === 0) {
                delete carrinho[idProduto];
            }
            atualizarDisplay(idProduto);
            atualizarCarrinho();
            
            // Remover do servidor se quantidade chegou a 0
            if (carrinho[idProduto] === undefined) {
                removerDoCarrinhoServidor(idProduto);
            }
        }
    }
    
    function adicionarAoCarrinhoServidor(idProduto, quantidade) {
        const formData = new FormData();
        formData.append('add_to_cart', '1');
        formData.append('produto_id', idProduto);
        formData.append('quantidade', quantidade);
        
        fetch('./carrinho.php', {
            method: 'POST',
            body: formData
        }).catch(error => {
            console.error('Erro ao adicionar ao carrinho:', error);
        });
    }
    
    function removerDoCarrinhoServidor(idProduto) {
        const formData = new FormData();
        formData.append('remove_item', '1');
        formData.append('produto_id', idProduto);
        
        fetch('./carrinho.php', {
            method: 'POST',
            body: formData
        }).catch(error => {
            console.error('Erro ao remover do carrinho:', error);
        });
    }

    function atualizarDisplay(idProduto) {
        const qtyDisplay = document.getElementById(`qty-${idProduto}`);
        if (qtyDisplay) {
            qtyDisplay.textContent = carrinho[idProduto] || 0;
        }
    }

    function atualizarCarrinho() {
        const cartCount = document.querySelector('.cart-count');
        const totalItens = Object.values(carrinho).reduce((sum, qty) => sum + qty, 0);
        
        cartCount.textContent = totalItens;
        cartCount.style.display = totalItens > 0 ? 'block' : 'none';
        
        // Atualizar carrinho flutuante
        if (totalItens > 0) {
            mostrarCarrinhoFlutuante();
        } else {
            fecharCarrinho();
        }
    }

    function mostrarCarrinhoFlutuante() {
        const floatingCart = document.getElementById('floatingCart');
        floatingCart.style.display = 'block';
        
        // Aqui você implementaria a lógica para mostrar os itens
        // Por enquanto, apenas mostra o carrinho
    }

    function verCarrinho() {
        const floatingCart = document.getElementById('floatingCart');
        floatingCart.style.display = floatingCart.style.display === 'none' ? 'block' : 'none';
    }

    function fecharCarrinho() {
        const floatingCart = document.getElementById('floatingCart');
        floatingCart.style.display = 'none';
    }

    function finalizarPedido() {
        if (Object.keys(carrinho).length === 0) {
            alert('Seu carrinho está vazio!');
            return;
        }
        
        // Redirecionar para página do carrinho
        window.location.href = './carrinho.php';
    }

    // Busca em tempo real
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado - inicializando eventos do cardápio');
        
        const searchForm = document.querySelector('.search-form');
        const searchInput = searchForm.querySelector('input[name="busca"]');
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.submit();
            }
        });

        // Adicionar eventos de clique para categorias (fallback)
        const categoryItems = document.querySelectorAll('.category-item');
        console.log('Categorias encontradas:', categoryItems.length);
        
        categoryItems.forEach(function(item, index) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Categoria clicada:', index);
                
                // Remover classe active de todos os itens
                categoryItems.forEach(cat => cat.classList.remove('active'));
                
                // Adicionar classe active ao item clicado
                this.classList.add('active');
                
                // Obter categoria do data attribute
                const categoria = this.getAttribute('data-categoria') || '';
                console.log('Categoria selecionada:', categoria);
                
                filtrarCategoria(categoria);
            });
        });

        // Adicionar evento para botão limpar filtros
        const clearButton = document.querySelector('.clear-filter');
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                console.log('Botão limpar filtros clicado');
                limparFiltros();
            });
        }
    });
</script>

<?php 
include_once "./components/_base-footer.php";
?>
<?php 
$titulo = "inicio";
include_once "../components/_base-header.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION["id"])) {
    header("Location: ./login.php");
    exit();
}

// Carregar foto de perfil na sessão se não estiver definida
if (!isset($_SESSION['foto_perfil'])) {
    try {
        require_once '../models/Crud_usuario.php';
        $crudUsuario = new Crud_usuario();
        $crudUsuario->setId_usuario($_SESSION["id"]);
        $dadosUsuario = $crudUsuario->read();
        if ($dadosUsuario && isset($dadosUsuario['imagem_perfil'])) {
            $_SESSION['foto_perfil'] = $dadosUsuario['imagem_perfil'];
        }
    } catch (Exception $e) {
        // Silenciar erro para não quebrar a página
    }
}

// Incluir controladores necessários
require_once '../models/Crud_produto.php';
require_once '../models/Crud_categoria.php';
require_once '../helpers/image_helper.php';

// Criar instâncias para buscar dados
$crudProduto = new Crud_produto();
$crudCategoria = new Crud_categoria();

// Buscar produtos em destaque (limitando a 6 para não sobrecarregar a página)
$produtos = $crudProduto->read();
$produtosDestaque = array_slice($produtos, 0, 6); // Pegar apenas os 6 primeiros

// Buscar categorias usando o método correto
$categorias = $crudCategoria->readAll();

// Função para obter imagem padrão baseada no nome da categoria
function getImagemPadrao($nome_categoria) {
    $nome_lower = strtolower($nome_categoria);
    
    if (strpos($nome_lower, 'bebida') !== false || strpos($nome_lower, 'drink') !== false || strpos($nome_lower, 'smoothie') !== false) {
        return 'assets/bebidas.png';
    } elseif (strpos($nome_lower, 'bowl') !== false || strpos($nome_lower, 'salada') !== false) {
        return 'assets/bowls.png';
    } elseif (strpos($nome_lower, 'lanche') !== false || strpos($nome_lower, 'sanduiche') !== false || strpos($nome_lower, 'pão') !== false || strpos($nome_lower, 'toast') !== false) {
        return 'assets/lanches.png';
    } elseif (strpos($nome_lower, 'sobremesa') !== false || strpos($nome_lower, 'doce') !== false) {
        return 'assets/sobremesa.png';
    } elseif (strpos($nome_lower, 'carne') !== false || strpos($nome_lower, 'proteina') !== false || strpos($nome_lower, 'grelhado') !== false) {
        return 'assets/carnes.png';
    } elseif (strpos($nome_lower, 'sopa') !== false) {
        return 'assets/sopas.png';
    } elseif (strpos($nome_lower, 'massa') !== false || strpos($nome_lower, 'noodle') !== false) {
        return 'assets/massas.png';
    } elseif (strpos($nome_lower, 'molho') !== false || strpos($nome_lower, 'condimento') !== false) {
        return 'assets/molhos.png';
    } elseif (strpos($nome_lower, 'frito') !== false || strpos($nome_lower, 'petisco') !== false || strpos($nome_lower, 'entrada') !== false) {
        return 'assets/fritos.png';
    } else {
        return 'assets/bowls.png'; // padrão
    }
}

?>
<link rel="stylesheet" href="../styles/inicio_produtos.css">
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
                    <input type="text" id="searchInput" placeholder="O que você quer comer hoje...Kelvin?Eu também!" autocomplete="off">
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>
                <div class="top-actions">
                    <button class="icon-btn">
                        <i class="fas fa-comment"></i>
                    </button>
                    <button class="icon-btn" onclick="window.location.href='configuracoes.php#favoritos'" title="Meus Favoritos">
                        <i class="fas fa-star"></i>
                    </button>
                    <div class="avatar">
                        <img src="<?= !empty($_SESSION['foto_perfil']) ? './images/usuarios/' . $_SESSION['foto_perfil'] : './images/usuário.jpeg' ?>" alt="Usuário">
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
                    <a href="cardapio.php" class="see-all">Ver tudo...</a>
                </div>
                <div class="category-list">
                    <?php if (!empty($categorias)): ?>
                        <?php foreach (array_slice($categorias, 0, 6) as $index => $categoria): ?>
                            <div class="category-item <?= $index === 0 ? 'active' : '' ?>" 
                                 data-categoria-id="<?= $categoria['id_categoria'] ?>"
                                 data-categoria-nome="<?= htmlspecialchars($categoria['nome_categoria']) ?>"
                                 onclick="navegarCategoria('<?= htmlspecialchars($categoria['nome_categoria']) ?>')">>
                                <div class="category-icon">
                                    <?php if (!empty($categoria['imagem'])): ?>
                                        <img src="../images/categorias/<?= htmlspecialchars($categoria['imagem']) ?>" 
                                             alt="<?= htmlspecialchars($categoria['nome_categoria']) ?>"
                                             onerror="this.src='<?= getImagemPadrao($categoria['nome_categoria']) ?>'">
                                    <?php else: ?>
                                        <img src="<?= getImagemPadrao($categoria['nome_categoria']) ?>" 
                                             alt="<?= htmlspecialchars($categoria['nome_categoria']) ?>">
                                    <?php endif; ?>
                                </div>
                                <span><?= htmlspecialchars($categoria['nome_categoria']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Categorias padrão caso não haja no banco -->
                        <div class="category-item active" onclick="navegarCategoria('todas')">
                            <div class="category-icon">
                                <img src="assets/bebidas.png" alt="Bebidas">
                            </div>
                            <span>Bebidas</span>
                        </div>
                        <div class="category-item" onclick="navegarCategoria('todas')">
                            <div class="category-icon">
                                <img src="assets/bowls.png" alt="Bowls">
                            </div>
                            <span>Bowls</span>
                        </div>
                        <div class="category-item" onclick="navegarCategoria('todas')">
                            <div class="category-icon">
                                <img src="assets/lanches.png" alt="Lanches">
                            </div>
                            <span>Lanches</span>
                        </div>
                        <div class="category-item" onclick="navegarCategoria('todas')">
                            <div class="category-icon">
                                <img src="assets/sobremesa.png" alt="Sobremesa">
                            </div>
                            <span>Sobremesa</span>
                        </div>
                        <div class="category-item" onclick="navegarCategoria('todas')">
                            <div class="category-icon">
                                <img src="assets/carnes.png" alt="Carnes">
                            </div>
                            <span>Carnes</span>
                        </div>
                    <?php endif; ?>
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
                                            <img src="<?php echo getImageSrc($produto['imagem']); ?>" 
                                                 alt="<?php echo htmlspecialchars($produto['nome_produto']); ?>">
                                        <?php else: ?>
                                            <img src="../assets/avatar.png" alt="Produto sem imagem">
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
    </div>
            
    <script>
        // Função para navegar para categoria
        function navegarCategoria(categoriaNome) {
            // Adicionar efeito de loading
            const categoriaElement = event.target.closest('.category-item');
            if (categoriaElement) {
                categoriaElement.style.opacity = '0.7';
                categoriaElement.style.transform = 'scale(0.95)';
            }
            
            // Navegar após breve delay para mostrar feedback
            setTimeout(() => {
                if (categoriaNome === 'todas') {
                    window.location.href = 'cardapio.php';
                } else {
                    window.location.href = `cardapio.php?categoria=${encodeURIComponent(categoriaNome)}`;
                }
            }, 200);
        }

        // Funcionalidade de busca com sugestões
        const searchInput = document.querySelector('#searchInput');
        const suggestionsContainer = document.querySelector('#searchSuggestions');
        let searchTimeout;
        let selectedIndex = -1;
        
        if (searchInput && suggestionsContainer) {
            // Buscar sugestões enquanto digita
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    hideSuggestions();
                    return;
                }
                
                // Debounce para evitar muitas requisições
                searchTimeout = setTimeout(() => {
                    fetchSuggestions(query);
                }, 300);
            });
            
            // Navegação com teclado
            searchInput.addEventListener('keydown', function(e) {
                const suggestions = suggestionsContainer.querySelectorAll('.suggestion-item');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, suggestions.length - 1);
                    updateSelection(suggestions);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    updateSelection(suggestions);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (selectedIndex >= 0 && suggestions[selectedIndex]) {
                        selectProduct(suggestions[selectedIndex]);
                    } else {
                        const termo = this.value.trim();
                        if (termo) {
                            window.location.href = `cardapio.php?busca=${encodeURIComponent(termo)}`;
                        }
                    }
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });
            
            // Fechar sugestões ao clicar fora
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    hideSuggestions();
                }
            });
        }
        
        function fetchSuggestions(query) {
            suggestionsContainer.innerHTML = '<div class="suggestion-loading">🔍 Buscando...</div>';
            suggestionsContainer.classList.add('show');
            
            fetch(`./api/search_suggestions.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    displaySuggestions(data);
                })
                .catch(error => {
                    console.error('Erro ao buscar sugestões:', error);
                    suggestionsContainer.innerHTML = '<div class="suggestion-empty">❌ Erro ao buscar produtos</div>';
                });
        }
        
        function displaySuggestions(products) {
            selectedIndex = -1;
            
            if (products.length === 0) {
                suggestionsContainer.innerHTML = '<div class="suggestion-empty">😅 Nenhum produto encontrado</div>';
                return;
            }
            
            const html = products.map(product => `
                <div class="suggestion-item" data-product-id="${product.id}">
                    <img src="${product.imagem}" alt="${product.nome}" class="suggestion-image" onerror="this.src='../assets/default-food.png'">
                    <div class="suggestion-content">
                        <div class="suggestion-name">${product.nome}</div>
                        <div class="suggestion-details">
                            <span class="suggestion-category">${product.categoria}</span>
                            <span class="suggestion-price">${product.preco}</span>
                        </div>
                    </div>
                </div>
            `).join('');
            
            suggestionsContainer.innerHTML = html;
            
            // Adicionar eventos de clique
            suggestionsContainer.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', () => selectProduct(item));
            });
        }
        
        function updateSelection(suggestions) {
            suggestions.forEach((item, index) => {
                item.classList.toggle('active', index === selectedIndex);
            });
        }
        
        function selectProduct(item) {
            const productId = item.dataset.productId;
            window.location.href = `produto.php?id=${productId}`;
        }
        
        function hideSuggestions() {
            suggestionsContainer.classList.remove('show');
            selectedIndex = -1;
        }

        // Melhorar interação com categorias
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
        // Feedback visual para botões de adicionar ao carrinho
        document.querySelectorAll('.add-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Prevenir que o clique no botão ative o link do produto
                e.stopPropagation();
                
                // Verificar se o produto está em estoque
                if (this.disabled) {
                    return false;
                }
                
                // Adicionar efeito de clique
                this.classList.add('clicked');
                
                // Criar elemento de feedback moderno
                const feedback = document.createElement('div');
                feedback.innerHTML = '<i class="fas fa-check"></i> Adicionado!';
                feedback.style.cssText = `
                    position: absolute;
                    top: -40px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, #28a745, #20963d);
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                    opacity: 0;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    z-index: 1000;
                    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
                    white-space: nowrap;
                `;
                
                // Adicionar posição relativa ao card se não tiver
                const card = this.closest('.product-card');
                card.style.position = 'relative';
                card.appendChild(feedback);
                
                // Animação de entrada
                setTimeout(() => {
                    feedback.style.opacity = '1';
                    feedback.style.transform = 'translateX(-50%) translateY(-5px)';
                }, 10);
                
                // Efeito de sucesso no botão
                setTimeout(() => {
                    this.classList.add('success');
                    this.innerHTML = '<i class="fas fa-check"></i>';
                }, 200);
                
                // Voltar ao estado normal
                setTimeout(() => {
                    this.classList.remove('success');
                    this.innerHTML = '<i class="fas fa-plus"></i>';
                    this.classList.remove('clicked');
                }, 1500);
                
                // Remover feedback
                setTimeout(() => {
                    feedback.style.opacity = '0';
                    feedback.style.transform = 'translateX(-50%) translateY(-10px)';
                    setTimeout(() => {
                        if (feedback.parentNode) {
                            feedback.parentNode.removeChild(feedback);
                        }
                    }, 300);
                }, 2000);
                
                // Permitir que o formulário seja enviado normalmente
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
include_once "../components/_base-footer.php";
?>
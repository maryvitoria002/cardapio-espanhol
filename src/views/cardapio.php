<?php 
$titulo = "cardapio";
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
        require_once __DIR__ . '/../models/Crud_usuario.php';
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

// Incluir as classes necessárias
require_once __DIR__ . '/../db/conection.php';
require_once __DIR__ . '/../models/Crud_produto.php';
require_once __DIR__ . '/../models/Crud_categoria.php';
require_once __DIR__ . '/../helpers/image_helper.php';

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
                    <img src="<?= !empty($_SESSION['foto_perfil']) ? './images/usuarios/' . $_SESSION['foto_perfil'] : './images/usuário.jpeg' ?>" alt="Usuário">
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
                                    <img src="<?= getImageSrc($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                                <?php else: ?>
                                    <img src="../assets/cardapio.png" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                                <?php endif; ?>
                                <button class="favorite-btn" onclick="event.preventDefault(); event.stopPropagation(); toggleFavorito(<?= $produto['id_produto'] ?>)">
                                    <i class="far fa-heart"></i>
                                </button>
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
                        <form method="POST" action="carrinho.php" style="display: inline;" onclick="event.stopPropagation();">
                            <input type="hidden" name="acao" value="adicionar">
                            <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">
                            <input type="hidden" name="quantidade" value="1">
                            <button type="submit" class="add-btn" 
                                    <?= $produto['estoque'] <= 0 ? 'disabled' : '' ?>
                                    onclick="event.stopPropagation();">
                                <i class="fas fa-plus"></i>
                            </button>
                        </form>
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

        // Adicionar eventos de clique para categorias
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
    });

    // Função para favoritar
    function toggleFavorito(idProduto) {
        fetch('./process_favorito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle&id_produto=${idProduto}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const btnFavoritar = document.querySelector(`[onclick*="${idProduto}"] i`);
                if (btnFavoritar) {
                    if (data.action === 'added') {
                        btnFavoritar.classList.remove('far');
                        btnFavoritar.classList.add('fas');
                        btnFavoritar.style.color = '#e74c3c';
                        showNotification('Produto adicionado aos favoritos!', 'success');
                    } else {
                        btnFavoritar.classList.remove('fas');
                        btnFavoritar.classList.add('far');
                        btnFavoritar.style.color = '';
                        showNotification('Produto removido dos favoritos!', 'success');
                    }
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao processar favorito', 'error');
        });
    }

    function showNotification(message, type) {
        // Criar elemento de notificação
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        // Adicionar ao body
        document.body.appendChild(notification);
        
        // Mostrar com animação
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>

<style>
/* Botão de favorito */
.favorite-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    z-index: 2;
}

.favorite-btn:hover {
    background: white;
    transform: scale(1.1);
}

.favorite-btn i {
    color: #666;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.favorite-btn:hover i {
    color: #e74c3c;
}

.favorite-btn i.fas {
    color: #e74c3c !important;
}

/* Notificações */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 1000;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification.success {
    background: #27ae60;
}

.notification.error {
    background: #e74c3c;
}
</style>

<?php 
include_once "../components/_base-footer.php";
?>
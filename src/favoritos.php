<?php 
// Iniciar sessão se não estiver ativa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verificar se o usuário está logado ANTES de incluir o header
if (empty($_SESSION["id"])) {
    header("Location: ./login.php");
    exit();
}

$titulo = "favoritos";
include_once "./components/_base-header.php";

// Carregar foto de perfil na sessão se não estiver definida
if (!isset($_SESSION['foto_perfil'])) {
    try {
        require_once './controllers/usuario/Crud_usuario.php';
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
require_once './controllers/favorito/Crud_favorito.php';

// Criar instância para buscar favoritos
$crudFavorito = new Crud_favorito();
$favoritos = $crudFavorito->getFavoritosByUsuario($_SESSION["id"]);
?>

<link rel="stylesheet" href="./styles/favoritos.css">

<div class="container">
    <main class="main-content">
        <!-- Cabeçalho -->
        <header class="header">
            <div class="greeting">
                <h1>Meus Favoritos</h1>
                <p><?= count($favoritos) ?> <?= count($favoritos) == 1 ? 'produto favoritado' : 'produtos favoritados' ?></p>
            </div>
            <div class="top-actions">
                <div class="avatar">
                    <img src="<?= !empty($_SESSION['foto_perfil']) ? './images/usuarios/' . $_SESSION['foto_perfil'] : './images/usuário.jpeg' ?>" alt="Usuário">
                    <span class="status"></span>
                </div>
            </div>
        </header>

        <!-- Lista de Favoritos -->
        <section class="favorites-section">
            <?php if (empty($favoritos)): ?>
                <div class="empty-favorites">
                    <i class="fas fa-heart"></i>
                    <h3>Nenhum produto favoritado ainda</h3>
                    <p>Comece a adicionar produtos aos seus favoritos para vê-los aqui!</p>
                    <a href="cardapio.php" class="btn-primary">Explorar Cardápio</a>
                </div>
            <?php else: ?>
                <div class="favorites-grid">
                    <?php foreach ($favoritos as $produto): ?>
                        <div class="product-card" data-produto-id="<?= $produto['id_produto'] ?>">
                            <div class="product-image">
                                <?php if (!empty($produto['imagem'])): ?>
                                    <img src="./images/comidas/<?= htmlspecialchars($produto['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($produto['nome_produto']) ?>"
                                         onerror="this.src='./assets/avatar.png'">
                                <?php else: ?>
                                    <img src="./assets/avatar.png" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                                <?php endif; ?>
                                <button class="favorite-btn active" onclick="toggleFavorito(<?= $produto['id_produto'] ?>)">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($produto['nome_produto']) ?></h3>
                                <p class="product-description"><?= htmlspecialchars($produto['descricao']) ?></p>
                                <div class="product-footer">
                                    <span class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                                    <button class="btn-add-cart" onclick="adicionarAoCarrinho(<?= $produto['id_produto'] ?>)">
                                        <i class="fas fa-shopping-cart"></i>
                                        Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<script>
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
            if (data.action === 'removed') {
                // Remove o produto da página
                const productCard = document.querySelector(`[data-produto-id="${idProduto}"]`);
                if (productCard) {
                    productCard.remove();
                    
                    // Atualizar contador
                    const greeting = document.querySelector('.greeting p');
                    const currentCount = parseInt(greeting.textContent.split(' ')[0]) - 1;
                    greeting.textContent = `${currentCount} ${currentCount == 1 ? 'produto favoritado' : 'produtos favoritados'}`;
                    
                    // Se não há mais favoritos, mostrar mensagem vazia
                    if (currentCount === 0) {
                        location.reload();
                    }
                }
            }
            
            // Mostrar notificação
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao processar favorito', 'error');
    });
}

function adicionarAoCarrinho(idProduto) {
    fetch('./carrinho.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&produto_id=${idProduto}&quantidade=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Produto adicionado ao carrinho!', 'success');
        } else {
            showNotification(data.message || 'Erro ao adicionar produto', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao adicionar produto ao carrinho', 'error');
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

<?php include_once "./components/_base-footer.php"; ?>
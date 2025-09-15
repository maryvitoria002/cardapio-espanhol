<?php 
$titulo = "configuracoes";
include_once "../components/_base-header.php";
require_once "../models/Crud_usuario.php";
require_once "../models/Crud_favorito.php";

if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

$usuario = new Crud_usuario();
$usuario->setId_usuario($_SESSION['id']);
$dadosUsuario = $usuario->read();

// Buscar favoritos do usuário
$crudFavorito = new Crud_favorito();
$favoritos = $crudFavorito->getFavoritosByUsuario($_SESSION['id']);

// Verificar se os dados do usuário foram encontrados
if (!$dadosUsuario) {
    echo "<script>alert('Erro ao carregar dados do usuário. Faça login novamente.'); window.location.href = './login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $primeiro_nome = $_POST['primeiro_nome'] ?? '';
    $segundo_nome = $_POST['segundo_nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    $usuario->setPrimeiro_nome($primeiro_nome);
    $usuario->setSegundo_nome($segundo_nome);
    $usuario->setTelefone($telefone);
    $usuario->setData_atualizacao();

    if ($usuario->update()) {
        echo "<script>alert('Informações atualizadas com sucesso!'); window.location.href = './configuracoes.php';</script>";
        exit();
    } else {
        echo "<script>alert('Erro ao atualizar informações. Tente novamente.');</script>";
    }
}
?>

<div class="config-container">
    <div class="retangulo_branco">
        <div class="fotoperfil">
            <img id="foto-perfil-img" src="<?= !empty($dadosUsuario['imagem_perfil']) ? './images/usuarios/' . htmlspecialchars($dadosUsuario['imagem_perfil']) : './images/usuário.jpeg' ?>" alt="foto de perfil">
            <button class="btn-change-photo" onclick="document.getElementById('input-foto').click()">
                <i class="fas fa-camera"></i>
            </button>
            <input type="file" id="input-foto" accept="image/*" style="display: none;" onchange="uploadFotoPerfil(this)">
        </div>
        <div class="nomeperfil">
           <div class="nome">
               <h2><?= htmlspecialchars($dadosUsuario['primeiro_nome'] . ' ' . $dadosUsuario['segundo_nome']) ?></h2>
           </div>
           <div class="cargo">Cliente</div>
        </div>

        <nav class="menu-config">
            <a href="#" class="menu-item active" data-section="informacoes">
                <img src="../assets/avatar.png" alt="">
                <span>Informações Pessoais</span>
            </a>

            <a href="#" class="menu-item" data-section="seguranca">
                <img src="../assets/cadeado.png" alt="">
                <span>Login e Senha</span>
            </a>

            <a href="#" class="menu-item" data-section="favoritos">
                <img src="../assets/estrela.png" alt="">
                <span>Itens Favoritos</span>
            </a>
        </nav>
    </div>

    <div class="retangulo_branco2">
        <!-- Seção pra atualizar as informações Pessoais -->
        <div class="config-section active" id="informacoes">
            <h1>Informações Pessoais</h1>
            <form class="form-config" method="POST" action="./configuracoes.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="primeiro_nome" value="<?= htmlspecialchars($dadosUsuario['primeiro_nome']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Sobrenome</label>
                        <input type="text" name="segundo_nome" value="<?= htmlspecialchars($dadosUsuario['segundo_nome']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($dadosUsuario['email']) ?>" readonly>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="tel" name="telefone" value="<?= htmlspecialchars($dadosUsuario['telefone'] ?? '') ?>">
                    </div>
                </div>
                
                <button type="submit" class="btn-save" name="update">Salvar Alterações</button>
            </form>
        </div>

        <!-- Seção de Segurança -->
        <div class="config-section" id="seguranca">
            <h1>Login e Senha</h1>
            <form class="form-config">
                <div class="form-group">
                    <label>Senha Atual</label>
                    <input type="password">
                </div>
                <div class="form-group">
                    <label>Nova Senha</label>
                    <input type="password">
                </div>
                <div class="form-group">
                    <label>Confirmar Nova Senha</label>
                    <input type="password">
                </div>
                <button type="submit" class="btn-save">Alterar Senha</button>
            </form>
        </div>

        <!-- Seção de Favoritos -->
        <div class="config-section" id="favoritos">
            <h1>Itens Favoritos</h1>
            <?php if (empty($favoritos)): ?>
                <div class="empty-favorites">
                    <i class="fas fa-heart"></i>
                    <h3>Nenhum produto favoritado ainda</h3>
                    <p>Comece a adicionar produtos aos seus favoritos para vê-los aqui!</p>
                    <a href="cardapio.php" class="btn-primary">Explorar Cardápio</a>
                </div>
            <?php else: ?>
                <div class="favorites-counter">
                    <p><?= count($favoritos) ?> <?= count($favoritos) == 1 ? 'produto favoritado' : 'produtos favoritados' ?></p>
                </div>
                <div class="favorites-grid-config">
                    <?php foreach ($favoritos as $produto): ?>
                        <div class="product-card-config" data-produto-id="<?= $produto['id_produto'] ?>">
                            <div class="product-image-config">
                                <?php if (!empty($produto['imagem'])): ?>
                                    <img src="./images/comidas/<?= htmlspecialchars($produto['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($produto['nome_produto']) ?>"
                                         onerror="this.src='./assets/avatar.png'">
                                <?php else: ?>
                                    <img src="./assets/avatar.png" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                                <?php endif; ?>
                                <button class="favorite-btn-config active" onclick="toggleFavorito(<?= $produto['id_produto'] ?>)">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info-config">
                                <h4><?= htmlspecialchars($produto['nome_produto']) ?></h4>
                                <p class="product-description-config"><?= htmlspecialchars(substr($produto['descricao'], 0, 80)) ?>...</p>
                                <div class="product-footer-config">
                                    <span class="price-config">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                                    <button class="btn-add-cart-config" onclick="adicionarAoCarrinho(<?= $produto['id_produto'] ?>)">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Detectar hash na URL para mostrar seção específica
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash.substring(1); // Remove o #
    if (hash && document.getElementById(hash)) {
        // Remove active de todos os itens
        document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.config-section').forEach(s => s.classList.remove('active'));
        
        // Ativa o item correspondente ao hash
        const menuItem = document.querySelector(`[data-section="${hash}"]`);
        const section = document.getElementById(hash);
        
        if (menuItem && section) {
            menuItem.classList.add('active');
            section.classList.add('active');
        }
    }
});

document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Remove active class from all items
        document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.config-section').forEach(s => s.classList.remove('active'));
        
        // Add active class to clicked item
        item.classList.add('active');
        
        // Show corresponding section
        const section = item.dataset.section;
        document.getElementById(section).classList.add('active');
    });
});

function uploadFotoPerfil(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('foto_perfil', input.files[0]);
        
        // Mostrar loading
        const img = document.getElementById('foto-perfil-img');
        const originalSrc = img.src;
        
        fetch('./upload_foto_perfil.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar imagem com timestamp para forçar reload
                img.src = data.foto_url + '?t=' + new Date().getTime();
                
                // Atualizar todas as imagens de perfil na página
                updateAllProfileImages(data.foto_url);
                
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
                input.value = ''; // Limpar input
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao fazer upload da foto', 'error');
            input.value = ''; // Limpar input
        });
    }
}

function updateAllProfileImages(newImageUrl) {
    // Atualizar todas as imagens de perfil na aplicação
    const profileImages = document.querySelectorAll('img[alt="Usuário"], img[alt="foto de perfil"]');
    profileImages.forEach(img => {
        img.src = newImageUrl + '?t=' + new Date().getTime();
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
                    const counter = document.querySelector('.favorites-counter p');
                    if (counter) {
                        const currentCount = parseInt(counter.textContent.split(' ')[0]) - 1;
                        counter.textContent = `${currentCount} ${currentCount == 1 ? 'produto favoritado' : 'produtos favoritados'}`;
                        
                        // Se não há mais favoritos, recarregar a página para mostrar mensagem vazia
                        if (currentCount === 0) {
                            location.reload();
                        }
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
</script>

<?php 
include_once "../components/_base-footer.php";
?>


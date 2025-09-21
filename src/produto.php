<?php 
session_start();
require_once "./controllers/produto/Crud_produto.php";
require_once "./controllers/categoria/Crud_categoria.php";
require_once "./controllers/avaliacao/Crud_avaliacao.php";
require_once "./helpers/image_helper.php";

// Verificar se o usuário está logado ANTES de incluir o header
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

$titulo = "produto";
include_once "./components/_base-header.php";

// Verificar se o ID do produto foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ./cardapio.php");
    exit();
}

$produto_id = $_GET['id'];

// Buscar dados do produto
$produto = new Crud_produto();
$produto->setId_produto($produto_id);
$dadosProduto = $produto->read();

if (!$dadosProduto) {
    header("Location: ./cardapio.php");
    exit();
}

// Buscar categoria do produto
$categoria = new Crud_categoria();
$categoria->setId_categoria($dadosProduto['id_categoria']);
$dadosCategoria = $categoria->read();

// Buscar avaliações do produto
$avaliacao = new Crud_avaliacao();
$avaliacoes = $avaliacao->readByProduto($produto_id);

// Calcular média das avaliações
$totalAvaliacoes = count($avaliacoes);
$somaNotas = 0;
if ($totalAvaliacoes > 0) {
    foreach ($avaliacoes as $aval) {
        $somaNotas += $aval['nota'];
    }
    $mediaAvaliacoes = round($somaNotas / $totalAvaliacoes, 1);
} else {
    $mediaAvaliacoes = 0;
}

// Processar adição ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantidade = (int)($_POST['quantidade'] ?? 1);
    
    // Inicializar carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    
    // Criar item do carrinho
    $item_carrinho = [
        'id_produto' => $produto_id,
        'nome' => $dadosProduto['nome_produto'],
        'preco' => $dadosProduto['preco'],
        'quantidade' => $quantidade,
        'imagem' => $dadosProduto['imagem'],
        'categoria' => $dadosCategoria['nome_categoria'] ?? ''
    ];
    
    // Verificar se o produto já está no carrinho
    $found = false;
    foreach ($_SESSION['carrinho'] as &$item) {
        if ($item['id_produto'] == $produto_id) {
            $item['quantidade'] += $quantidade;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['carrinho'][] = $item_carrinho;
    }
    
    // Redirecionar para evitar resubmissão
    header('Location: produto.php?id=' . $produto_id . '&added=1');
    exit();
}

// Verificar se produto foi adicionado
$produtoAdicionado = isset($_GET['added']) && $_GET['added'] == '1';
?>

<div class="produto-container">
    <?php if ($produtoAdicionado): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Produto adicionado ao carrinho com sucesso!
            <a href="carrinho.php" class="btn-ver-carrinho">Ver Carrinho</a>
        </div>
    <?php endif; ?>
    
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="./cardapio.php"><i class="fas fa-utensils"></i> Cardápio</a>
        <span>/</span>
        <a href="./cardapio.php?categoria=<?= $dadosCategoria['id_categoria'] ?? '' ?>"><?= htmlspecialchars($dadosCategoria['nome_categoria'] ?? 'Categoria') ?></a>
        <span>/</span>
        <span class="current"><?= htmlspecialchars($dadosProduto['nome_produto']) ?></span>
    </nav>

    <div class="produto-content">
        <!-- Imagem do produto -->
        <div class="produto-imagem">
            <div class="imagem-principal">
                <?php if (!empty($dadosProduto['imagem']) && imageExists($dadosProduto['imagem'])): ?>
                    <img src="<?= getImageSrc($dadosProduto['imagem']) ?>" 
                         alt="<?= htmlspecialchars($dadosProduto['nome_produto']) ?>" 
                         id="imagemPrincipal">
                <?php else: ?>
                    <img src="./assets/cardapio.png" 
                         alt="<?= htmlspecialchars($dadosProduto['nome_produto']) ?>" 
                         id="imagemPrincipal">
                <?php endif; ?>
                
                <!-- Badges -->
                <div class="produto-badges">
                    <?php if ($dadosProduto['promocao'] ?? false): ?>
                        <span class="badge badge-promo">Promoção</span>
                    <?php endif; ?>
                    <?php if ($mediaAvaliacoes >= 4.5): ?>
                        <span class="badge badge-popular">Popular</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Informações do produto -->
        <div class="produto-info">
            <div class="info-header">
                <h1 class="produto-nome"><?= htmlspecialchars($dadosProduto['nome_produto']) ?></h1>
                <div class="produto-categoria">
                    <i class="fas fa-tag"></i>
                    <span><?= htmlspecialchars($dadosCategoria['nome_categoria'] ?? 'Categoria') ?></span>
                </div>
            </div>

            <div class="produto-rating">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?= $i <= $mediaAvaliacoes ? 'filled' : '' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="rating-text"><?= $mediaAvaliacoes ?>/5</span>
                <span class="rating-count">(<?= $totalAvaliacoes ?> avaliações)</span>
            </div>

            <div class="produto-descricao">
                <h3>Descrição</h3>
                <p><?= htmlspecialchars($dadosProduto['descricao'] ?? 'Delicioso produto do nosso cardápio.') ?></p>
            </div>

            <?php if (!empty($dadosProduto['ingredientes'])): ?>
                <div class="produto-ingredientes">
                    <h3>Ingredientes</h3>
                    <p><?= htmlspecialchars($dadosProduto['ingredientes']) ?></p>
                </div>
            <?php endif; ?>

            <div class="produto-info-extra">
                <?php if (!empty($dadosProduto['tempo_preparo'])): ?>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>Tempo de preparo: <?= htmlspecialchars($dadosProduto['tempo_preparo']) ?> min</span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($dadosProduto['calorias'])): ?>
                    <div class="info-item">
                        <i class="fas fa-fire"></i>
                        <span><?= htmlspecialchars($dadosProduto['calorias']) ?> kcal</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Preço e ações -->
            <div class="produto-compra">
                <div class="preco-container">
                    <span class="preco-atual">R$ <?= number_format($dadosProduto['preco'], 2, ',', '.') ?></span>
                    <?php if ($dadosProduto['preco_promocional'] ?? false): ?>
                        <span class="preco-original">R$ <?= number_format($dadosProduto['preco_promocional'], 2, ',', '.') ?></span>
                    <?php endif; ?>
                </div>

                <form method="POST" class="form-compra">
                    <div class="quantidade-container">
                        <label for="quantidade">Cantidad:</label>
                        <div class="quantidade-controles">
                            <button type="button" class="qty-btn minus" onclick="alterarQuantidade(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="10" readonly>
                            <button type="button" class="qty-btn plus" onclick="alterarQuantidade(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="acoes-produto">
                        <button type="submit" name="add_to_cart" class="btn-adicionar-carrinho">
                            <i class="fas fa-shopping-cart"></i>
                            Agregar al Carrito
                        </button>
                        
                        <button type="button" class="btn-favoritar" onclick="toggleFavorito(<?= $dadosProduto['id_produto'] ?>)">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>

                    <div class="total-preco">
                        <span>Total: R$ <span id="precoTotal"><?= number_format($dadosProduto['preco'], 2, ',', '.') ?></span></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
// Variável global para o preço unitário
const precoUnitario = <?= $dadosProduto['preco'] ?>;

// Função para alterar quantidade
function alterarQuantidade(delta) {
    const quantidadeInput = document.getElementById('quantidade');
    let quantidade = parseInt(quantidadeInput.value) + delta;
    
    if (quantidade < 1) quantidade = 1;
    if (quantidade > 10) quantidade = 10;
    
    quantidadeInput.value = quantidade;
    atualizarPrecoTotal();
}

// Função para atualizar preço total
function atualizarPrecoTotal() {
    const quantidade = parseInt(document.getElementById('quantidade').value);
    const total = precoUnitario * quantidade;
    document.getElementById('precoTotal').textContent = total.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

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
            const btnFavoritar = document.querySelector('.btn-favoritar i');
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

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Atualizar preço ao carregar
    atualizarPrecoTotal();
    
    // Listener para mudança manual da quantidade
    document.getElementById('quantidade').addEventListener('change', atualizarPrecoTotal);
});
</script>

<style>
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
include_once "./components/_base-footer.php";
?>

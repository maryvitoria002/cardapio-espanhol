<?php 
$titulo = "produto";
include_once "./components/_base-header.php";
require_once "./controllers/produto/Crud_produto.php";
require_once "./controllers/categoria/Crud_categoria.php";
require_once "./controllers/avaliacao/Crud_avaliacao.php";

if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

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
                <?php if (!empty($dadosProduto['imagem']) && file_exists("./images/comidas/" . $dadosProduto['imagem'])): ?>
                    <img src="./images/comidas/<?= htmlspecialchars($dadosProduto['imagem']) ?>" 
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
                        <label for="quantidade">Quantidade:</label>
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
                            Adicionar ao Carrinho
                        </button>
                        
                        <button type="button" class="btn-favoritar" onclick="toggleFavorito()">
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

    <!-- Seção de avaliações -->
    <div class="avaliacoes-section">
        <h2>Avaliações dos Clientes</h2>
        
        <?php if ($totalAvaliacoes > 0): ?>
            <div class="avaliacoes-resumo">
                <div class="media-geral">
                    <span class="media-numero"><?= $mediaAvaliacoes ?></span>
                    <div class="media-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $mediaAvaliacoes ? 'filled' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="total-avaliacoes">Baseado em <?= $totalAvaliacoes ?> avaliações</span>
                </div>
            </div>

            <div class="avaliacoes-lista">
                <?php foreach (array_slice($avaliacoes, 0, 5) as $aval): ?>
                    <div class="avaliacao-item">
                        <div class="avaliacao-header">
                            <div class="usuario-info">
                                <div class="usuario-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="usuario-dados">
                                    <span class="usuario-nome"><?= htmlspecialchars($aval['nome_usuario'] ?? 'Cliente') ?></span>
                                    <span class="avaliacao-data"><?= date('d/m/Y', strtotime($aval['data_avaliacao'] ?? 'now')) ?></span>
                                </div>
                            </div>
                            <div class="avaliacao-nota">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= $aval['nota'] ? 'filled' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($aval['comentario'])): ?>
                            <div class="avaliacao-comentario">
                                <p><?= htmlspecialchars($aval['comentario']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalAvaliacoes > 5): ?>
                <div class="ver-mais-avaliacoes">
                    <button class="btn-ver-mais">Ver todas as avaliações (<?= $totalAvaliacoes ?>)</button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="sem-avaliacoes">
                <i class="far fa-star"></i>
                <h3>Ainda não há avaliações</h3>
                <p>Seja o primeiro a avaliar este produto!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Produtos relacionados -->
    <div class="produtos-relacionados">
        <h2>Produtos Relacionados</h2>
        <div class="produtos-grid">
            <?php
            // Buscar produtos da mesma categoria
            $produtosRelacionados = $produto->readByCategoria($dadosProduto['id_categoria'], 4, $produto_id);
            
            if ($produtosRelacionados):
                foreach ($produtosRelacionados as $prod):
            ?>
                <div class="produto-card-mini">
                    <a href="./produto.php?id=<?= $prod['id_produto'] ?>">
                        <div class="produto-imagem-mini">
                            <?php if (!empty($prod['imagem']) && file_exists("./images/comidas/" . $prod['imagem'])): ?>
                                <img src="./images/comidas/<?= htmlspecialchars($prod['imagem']) ?>" 
                                     alt="<?= htmlspecialchars($prod['nome_produto']) ?>">
                            <?php else: ?>
                                <img src="./assets/cardapio.png" 
                                     alt="<?= htmlspecialchars($prod['nome_produto']) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="produto-info-mini">
                            <h4><?= htmlspecialchars($prod['nome_produto']) ?></h4>
                            <span class="preco">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></span>
                        </div>
                    </a>
                </div>
            <?php
                endforeach;
            else:
            ?>
                <p>Nenhum produto relacionado encontrado.</p>
            <?php endif; ?>
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
function toggleFavorito() {
    const btnFavoritar = document.querySelector('.btn-favoritar i');
    if (btnFavoritar.classList.contains('far')) {
        btnFavoritar.classList.remove('far');
        btnFavoritar.classList.add('fas');
        btnFavoritar.style.color = '#e9b004';
    } else {
        btnFavoritar.classList.remove('fas');
        btnFavoritar.classList.add('far');
        btnFavoritar.style.color = '';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Atualizar preço ao carregar
    atualizarPrecoTotal();
    
    // Listener para mudança manual da quantidade
    document.getElementById('quantidade').addEventListener('change', atualizarPrecoTotal);
});
</script>

<?php 
include_once "./components/_base-footer.php";
?>

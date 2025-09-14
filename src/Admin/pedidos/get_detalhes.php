<?php
session_start();
require_once __DIR__ . '/../../db/conection.php';
require_once __DIR__ . '/../../controllers/pedido/Crud_pedido.php';

// Criar instância da conexão
$database = new Database();
$conexao = $database->getInstance();

// Verificar se está logado como admin
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

// Verificar se foi passado o ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do pedido não informado']);
    exit;
}

$idPedido = (int)$_GET['id'];

try {
    $crudPedido = new Crud_pedido($conexao);
    
    // Buscar dados do pedido
    $pedido = $crudPedido->readById($idPedido);
    
    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
        exit;
    }
    
    // Buscar produtos do pedido
    $sql_produtos = "SELECT pp.*, p.nome, p.descricao, p.imagem
                     FROM produto_pedido pp
                     JOIN produto p ON pp.id_produto = p.id_produto
                     WHERE pp.id_pedido = :id_pedido";
    
    $stmt = $conexao->prepare($sql_produtos);
    $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular total
    $total = $crudPedido->getTotalPedido($idPedido);
    
    // Gerar HTML dos detalhes
    ob_start();
?>
<div class="row">
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Informações do Pedido</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>ID do Pedido:</strong></td>
                <td>#<?= $pedido['id_pedido'] ?></td>
            </tr>
            <tr>
                <td><strong>Data/Hora:</strong></td>
                <td><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <?php
                    $badge_class = 'secondary';
                    switch($pedido['status_pedido']) {
                        case 'Pendente': $badge_class = 'warning'; break;
                        case 'Processando': $badge_class = 'info'; break;
                        case 'A caminho': $badge_class = 'primary'; break;
                        case 'Concluido': $badge_class = 'success'; break;
                        case 'Cancelado': $badge_class = 'danger'; break;
                    }
                    ?>
                    <span class="badge bg-<?= $badge_class ?>">
                        <?= htmlspecialchars($pedido['status_pedido']) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Valor Total:</strong></td>
                <td><strong class="text-success">R$ <?= number_format($total, 2, ',', '.') ?></strong></td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Dados do Cliente</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Nome:</strong></td>
                <td><?= htmlspecialchars($pedido['primeiro_nome'] . ' ' . $pedido['segundo_nome']) ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td><?= htmlspecialchars($pedido['email']) ?></td>
            </tr>
            <tr>
                <td><strong>Endereço:</strong></td>
                <td><?= htmlspecialchars($pedido['endereco'] ?? 'Não informado') ?></td>
            </tr>
        </table>
    </div>
</div>

<hr>

<h6 class="text-muted mb-3">Produtos do Pedido</h6>

<?php if (!empty($produtos)): ?>
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $produto): ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <?php if (!empty($produto['imagem'])): ?>
                        <img src="../../images/comidas/<?= htmlspecialchars($produto['imagem']) ?>" 
                             alt="<?= htmlspecialchars($produto['nome']) ?>"
                             class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                        <?php endif; ?>
                        <div>
                            <div class="fw-bold"><?= htmlspecialchars($produto['nome']) ?></div>
                            <?php if (!empty($produto['descricao'])): ?>
                            <small class="text-muted"><?= htmlspecialchars($produto['descricao']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-light text-dark"><?= $produto['quantidade'] ?>x</span>
                </td>
                <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                <td><strong>R$ <?= number_format($produto['preco'] * $produto['quantidade'], 2, ',', '.') ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="table-light">
            <tr>
                <th colspan="3" class="text-end">Total Geral:</th>
                <th><strong class="text-success">R$ <?= number_format($total, 2, ',', '.') ?></strong></th>
            </tr>
        </tfoot>
    </table>
</div>
<?php else: ?>
<div class="text-center p-4">
    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
    <p class="text-muted">Nenhum produto encontrado neste pedido.</p>
</div>
<?php endif; ?>

<?php if (!empty($pedido['motivo_cancelamento'])): ?>
<hr>
<h6 class="text-muted mb-3">Observações</h6>
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <?= nl2br(htmlspecialchars($pedido['motivo_cancelamento'])) ?>
</div>
<?php endif; ?>

<?php
    $html = ob_get_clean();
    
    echo json_encode([
        'success' => true, 
        'html' => $html,
        'pedido' => $pedido
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao carregar detalhes: ' . $e->getMessage()]);
}
?>

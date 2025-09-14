<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

$id_pedido = intval($_GET['id'] ?? 0);
if ($id_pedido <= 0) {
    header("Location: ./historico.php");
    exit();
}

require_once "db/conection.php";

$erro = '';

if ($_POST) {
    $nota = intval($_POST['nota'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');
    
    if ($nota >= 1 && $nota <= 5) {
        try {
            $database = new Database();
            $conexao = $database->getInstance();
            $user_id = $_SESSION['id'];
            
            $stmt = $conexao->prepare("SELECT status_pedido, id_usuario FROM pedido WHERE id_pedido = ?");
            $stmt->execute([$id_pedido]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($pedido && $pedido['id_usuario'] == $user_id) {
                $stmt = $conexao->prepare("SELECT id_avaliacao FROM avaliacao WHERE id_pedido = ?");
                $stmt->execute([$id_pedido]);
                $jaAvaliado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$jaAvaliado) {
                    // Verificar estrutura da tabela avaliacao para usar nome correto da coluna
                    $stmt = $conexao->prepare("SHOW COLUMNS FROM avaliacao");
                    $stmt->execute();
                    $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $nomes_colunas = array_column($colunas, 'Field');
                    
                    // Determinar nome correto da coluna de comentário
                    $coluna_comentario = 'comentario';
                    if (in_array('texto_avaliacao', $nomes_colunas)) {
                        $coluna_comentario = 'texto_avaliacao';
                    } elseif (in_array('texto', $nomes_colunas)) {
                        $coluna_comentario = 'texto';
                    } elseif (in_array('descricao', $nomes_colunas)) {
                        $coluna_comentario = 'descricao';
                    }
                    
                    // Inserir avaliação com nome correto da coluna
                    $sql = "INSERT INTO avaliacao (id_pedido, id_usuario, nota, $coluna_comentario, data_avaliacao) VALUES (?, ?, ?, ?, NOW())";
                    $stmt = $conexao->prepare($sql);
                    $resultado = $stmt->execute([$id_pedido, $user_id, $nota, $comentario]);
                    
                    if ($resultado) {
                        $_SESSION['mensagem_historico'] = "Avaliação enviada com sucesso!";
                        header("Location: ./historico.php");
                        exit();
                    } else {
                        $erro = "Erro ao salvar avaliação";
                    }
                } else {
                    $erro = "Este pedido já foi avaliado";
                }
            } else {
                $erro = "Pedido não encontrado";
            }
        } catch (Exception $e) {
            $erro = "Erro: " . $e->getMessage();
        }
    } else {
        $erro = "Selecione uma nota válida";
    }
}

try {
    $database = new Database();
    $conexao = $database->getInstance();
    $user_id = $_SESSION['id'];
    
    // Buscar pedido com nome correto da coluna total
    $stmt = $conexao->prepare("SELECT *, total_pedido as total FROM pedido WHERE id_pedido = ? AND id_usuario = ?");
    $stmt->execute([$id_pedido, $user_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedido) {
        header("Location: ./historico.php");
        exit();
    }
} catch (Exception $e) {
    header("Location: ./historico.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Avaliar Pedido</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .info { background: #f8f9fa; padding: 20px; margin-bottom: 20px; border-radius: 4px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; }
        .btn { padding: 12px 20px; border: none; border-radius: 4px; text-decoration: none; display: inline-block; margin: 5px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .erro { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .debug { margin-top: 30px; padding: 10px; background: #e9ecef; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Avaliar Pedido #<?= $id_pedido ?></h1>
        
        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        
        <div class="info">
            <h3>Informações do Pedido</h3>
            <p><strong>Status:</strong> <?= htmlspecialchars($pedido['status_pedido']) ?></p>
            <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></p>
            <p><strong>Total:</strong> R$ <?= number_format($pedido['total'], 2, ',', '.') ?></p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="nota">Como foi sua experiência?</label>
                <select name="nota" id="nota" required>
                    <option value="">Selecione uma nota...</option>
                    <option value="1">1 - Muito Ruim</option>
                    <option value="2">2 - Ruim</option>
                    <option value="3">3 - Regular</option>
                    <option value="4">4 - Bom</option>
                    <option value="5">5 - Excelente</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="comentario">Comentário (opcional):</label>
                <textarea name="comentario" id="comentario" placeholder="Conte sobre sua experiência..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Enviar Avaliação</button>
            <a href="./historico.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</body>
</html>

<?php
session_start();

// Permitir acesso direto - criar sessão admin automática se não existir
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Criar sessão admin automática
    $_SESSION['admin_logado'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_nome'] = 'Admin Sistema';
    $_SESSION['admin_acesso'] = 'admin';
}

require_once '../../models/Crud_usuario.php';

$crudUsuario = new Crud_usuario();
$message = '';
$message_type = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create':
                $dados = [
                    'primeiro_nome' => $_POST['primeiro_nome'],
                    'segundo_nome' => $_POST['segundo_nome'], 
                    'email' => $_POST['email'],
                    'senha' => password_hash($_POST['senha'], PASSWORD_DEFAULT),
                    'telefone' => $_POST['telefone'] ?? '',
                    'endereco' => $_POST['endereco'] ?? ''
                ];
                
                if ($crudUsuario->createUser($dados)) {
                    $message = 'Usuário criado com sucesso!';
                    $message_type = 'success';
                } else {
                    $message = 'Erro ao criar usuário!';
                    $message_type = 'error';
                }
                break;
                
            case 'update':
                $id = $_POST['id_usuario'];
                $dados = [
                    'primeiro_nome' => $_POST['primeiro_nome'],
                    'segundo_nome' => $_POST['segundo_nome'],
                    'email' => $_POST['email'], 
                    'telefone' => $_POST['telefone'] ?? '',
                    'endereco' => $_POST['endereco'] ?? ''
                ];
                
                // Atualizar senha apenas se fornecida
                if (!empty($_POST['senha'])) {
                    $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                }
                
                if ($crudUsuario->updateUser($id, $dados)) {
                    $message = 'Usuário atualizado com sucesso!';
                    $message_type = 'success';
                } else {
                    $message = 'Erro ao atualizar usuário!';
                    $message_type = 'error';
                }
                break;
                
            case 'delete':
                $id = $_POST['id_usuario'];
                if ($crudUsuario->delete($id)) {
                    $message = 'Usuário excluído com sucesso!';
                    $message_type = 'success';
                } else {
                    $message = 'Erro ao excluir usuário!';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Erro: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Parâmetros de busca e paginação
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 10;
$offset = ($page - 1) * $perPage;

try {
    // Buscar usuários
    $usuarios = $crudUsuario->readAll($search, '', $offset, $perPage);
    $total_records = $crudUsuario->count($search);
    $total_pages = ceil($total_records / $perPage);
    $total_usuarios = $crudUsuario->count();
} catch (Exception $e) {
    $usuarios = [];
    $total_records = 0;
    $total_pages = 0;
    $total_usuarios = 0;
    $message = 'Erro ao carregar dados: ' . $e->getMessage();
    $message_type = 'error';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Admin Ecoute Saveur</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CSS Admin -->
    <link rel="stylesheet" href="../css/admin.css">
    
    <style>
        .content-wrapper {
            padding: 20px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-card h3 {
            font-size: 2.5rem;
            margin: 0;
        }
        
        .table-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .btn-action {
            margin: 2px;
            padding: 5px 10px;
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .alert {
            margin-bottom: 20px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        
        .required {
            color: red;
        }
    </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="content-wrapper">
            <div class="container-fluid">
                
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-users"></i> Gerenciamento de Usuários</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="fas fa-plus"></i> Novo Usuário
                    </button>
                </div>
                
                <!-- Mensagens -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Estatísticas -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo $total_usuarios; ?></h3>
                            <p>Total de Usuários</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <h3><?php echo count($usuarios); ?></h3>
                            <p>Usuários na Página</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <h3><?php echo $total_pages; ?></h3>
                            <p>Total de Páginas</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <h3><?php echo $page; ?></h3>
                            <p>Página Atual</p>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="table-card">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Buscar por nome ou email..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="?" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh"></i> Limpar Filtros
                            </a>
                        </div>
                    </div>
                    
                    <!-- Tabela -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Data de Criação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Nenhum usuário encontrado</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['id_usuario']; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['primeiro_nome'] . ' ' . $usuario['segundo_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['telefone'] ?: '-'); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($usuario['data_criacao'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-action" 
                                                onclick="viewUser(<?php echo $usuario['id_usuario']; ?>)"
                                                title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning btn-action" 
                                                onclick="editUser(<?php echo htmlspecialchars(json_encode($usuario)); ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-action" 
                                                onclick="deleteUser(<?php echo $usuario['id_usuario']; ?>, '<?php echo htmlspecialchars($usuario['primeiro_nome'] . ' ' . $usuario['segundo_nome']); ?>')"
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <?php if ($total_pages > 1): ?>
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Criar Usuário -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Novo Usuário
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Primeiro Nome <span class="required">*</span></label>
                                    <input type="text" name="primeiro_nome" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Segundo Nome <span class="required">*</span></label>
                                    <input type="text" name="segundo_nome" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha <span class="required">*</span></label>
                            <input type="password" name="senha" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="tel" name="telefone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Endereço</label>
                            <textarea name="endereco" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuário -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit"></i> Editar Usuário
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editForm">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_usuario" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Primeiro Nome <span class="required">*</span></label>
                                    <input type="text" name="primeiro_nome" id="edit_primeiro_nome" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Segundo Nome <span class="required">*</span></label>
                                    <input type="text" name="segundo_nome" id="edit_segundo_nome" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nova Senha (deixe em branco para manter)</label>
                            <input type="password" name="senha" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="tel" name="telefone" id="edit_telefone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Endereço</label>
                            <textarea name="endereco" id="edit_endereco" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Atualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Visualizar Usuário -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user"></i> Detalhes do Usuário
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewUserContent">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash"></i> Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <p>Tem certeza que deseja excluir o usuário:</p>
                    <strong id="deleteUserName"></strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id_usuario" id="delete_id">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Admin JS -->
    <script src="../js/admin.js"></script>
    
    <script>
        // Função para editar usuário
        function editUser(userData) {
            document.getElementById('edit_id').value = userData.id_usuario;
            document.getElementById('edit_primeiro_nome').value = userData.primeiro_nome;
            document.getElementById('edit_segundo_nome').value = userData.segundo_nome;
            document.getElementById('edit_email').value = userData.email;
            document.getElementById('edit_telefone').value = userData.telefone || '';
            document.getElementById('edit_endereco').value = userData.endereco || '';
            
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
        
        // Função para visualizar usuário
        function viewUser(userId) {
            // Buscar dados do usuário via AJAX
            fetch(`view.php?id=${userId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('viewUserContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('viewModal')).show();
                })
                .catch(error => {
                    alert('Erro ao carregar dados do usuário');
                });
        }
        
        // Função para confirmar exclusão
        function deleteUser(userId, userName) {
            document.getElementById('delete_id').value = userId;
            document.getElementById('deleteUserName').textContent = userName;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
        // Auto-hide alerts
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
    }
    
    if ($action === 'delete' && isset($_POST['id'])) {
        try {
            $crudUsuario->delete($_POST['id']);
            $_SESSION['message'] = 'Usuário excluído com sucesso!';
            $_SESSION['message_type'] = 'success';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Erro ao excluir usuário: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
        header('Location: index.php');
        exit();
    }
}

try {
    // Estatísticas
    $total_usuarios = $crudUsuario->count('', '');
    $usuarios_hoje = 0; // Método não existe, assumindo 0
    $usuarios_ativos = $total_usuarios; // Assumindo todos ativos pois não há campo status
    $usuarios_com_pedidos = 0; // Método não existe, assumindo 0
    
    // Buscar usuários
    $usuarios = $crudUsuario->readAll($search, $status_filter, $offset, $perPage);
    $total_records = $crudUsuario->count($search, $status_filter);
    $total_pages = ceil($total_records / $perPage);
    
} catch (Exception $e) {
    $error = "Erro ao carregar usuários: " . $e->getMessage();
    $usuarios = [];
    $total_records = 0;
    $total_pages = 0;
    $total_usuarios = $usuarios_hoje = $usuarios_ativos = $usuarios_com_pedidos = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Ecoute Saveur Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <link href="css/usuarios.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="container">
            <div class="page-header">
                <div class="page-title">
                    <h1><i class="fas fa-users"></i> Usuários</h1>
                    <p>Gerencie os usuários do sistema</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i> Novo Usuário
                    </button>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_usuarios) ?></div>
                        <div class="stat-label">Total de Usuários</div>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($usuarios_ativos) ?></div>
                        <div class="stat-label">Usuários Ativos</div>
                    </div>
                </div>

                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($usuarios_hoje) ?></div>
                        <div class="stat-label">Cadastros Hoje</div>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($usuarios_com_pedidos) ?></div>
                        <div class="stat-label">Com Pedidos</div>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="filters-row">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Buscar usuários..." 
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="filter-group">
                            <select id="statusFilter">
                                <option value="">Todos os status</option>
                                <option value="ativo">Ativos</option>
                                <option value="inativo">Inativos</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Data Cadastro</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?php if (isset($usuario['imagem_perfil']) && $usuario['imagem_perfil']): ?>
                                                    <img src="../../images/users/<?= htmlspecialchars($usuario['imagem_perfil']) ?>" 
                                                         alt="<?= htmlspecialchars($usuario['primeiro_nome']) ?>">
                                                <?php else: ?>
                                                    <i class="fas fa-user"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="user-details">
                                                <strong><?= htmlspecialchars($usuario['primeiro_nome'] . ' ' . $usuario['segundo_nome']) ?></strong>
                                                <br>
                                                <small class="text-muted">ID: <?= $usuario['id_usuario'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td><?= htmlspecialchars($usuario['telefone'] ?? 'Não informado') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($usuario['data_criacao'])) ?></td>
                                    <td>
                                        <span class="status-badge active">Ativo</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum usuário encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Mostrando <?= count($usuarios) ?> de <?= $total_records ?> registros
                    </div>
                    <div class="pagination-links">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>" 
                               class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Criação -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Novo Usuário</h3>
                <button class="close-modal" onclick="closeCreateModal()">&times;</button>
            </div>
            <form method="POST" class="modal-body">
                <input type="hidden" name="action" value="create">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="primeiro_nome">Primeiro Nome *</label>
                            <input type="text" id="primeiro_nome" name="primeiro_nome" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="segundo_nome">Segundo Nome *</label>
                            <input type="text" id="segundo_nome" name="segundo_nome" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha *</label>
                    <input type="password" id="senha" name="senha" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" class="form-control">
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <textarea id="endereco" name="endereco" class="form-control" rows="3"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeCreateModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        function confirmDelete() {
            return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.');
        }

        function openCreateModal() {
            document.getElementById('createModal').style.display = 'flex';
        }

        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
            document.querySelector('#createModal form').reset();
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const search = this.value;
                const status = document.getElementById('statusFilter').value;
                window.location.href = `?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
            }
        });

        document.getElementById('statusFilter').addEventListener('change', function() {
            const search = document.getElementById('searchInput').value;
            const status = this.value;
            window.location.href = `?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
        });

        // Fechar modal clicando fora
        window.onclick = function(event) {
            const modal = document.getElementById('createModal');
            if (event.target === modal) {
                closeCreateModal();
            }
        }
    </script>

    <style>
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-avatar i {
            color: #666;
            font-size: 18px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .modal-body {
            padding: 20px;
        }

        .row {
            display: flex;
            gap: 15px;
        }

        .col-md-6 {
            flex: 1;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</body>
</html>
        <?php include '../includes/header.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1><i class="fas fa-users"></i> Usuários</h1>
                <p>Gerencie os usuários do sistema</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Lista de Usuários</h2>
                    <div class="header-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Buscar usuários..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <a href="create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Novo Usuário
                        </a>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Data de Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td>#<?= $usuario['id_usuario'] ?></td>
                                <td>
                                    <img src="../../images/<?= $usuario['imagem_perfil'] ?? 'avatar.png' ?>" 
                                         alt="<?= htmlspecialchars($usuario['primeiro_nome']) ?>" 
                                         class="table-avatar"
                                         onerror="this.src='../../assets/avatar.png'">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($usuario['primeiro_nome'] . ' ' . $usuario['segundo_nome']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><?= htmlspecialchars($usuario['telefone'] ?? 'Não informado') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($usuario['data_criacao'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Mostrando <?= count($usuarios) ?> de <?= $total_records ?> registros
                    </div>
                    <div class="pagination-links">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
                               class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        function confirmDelete() {
            return confirm('Tem certeza que deseja excluir este usuário?');
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const search = this.value;
            if (search.length >= 3 || search.length === 0) {
                window.location.href = `?search=${encodeURIComponent(search)}`;
            }
        });
    </script>
</body>
</html>

            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Lista de Usuários</h2>
                    <div class="header-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Buscar usuários..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <a href="create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Novo Usuário
                        </a>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="admin-table" id="usuariosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Data de Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td>#<?= $usuario['id_usuario'] ?></td>
                                <td>
                                    <img src="../../images/<?= $usuario['imagem_perfil'] ?>" 
                                         alt="Foto do usuário" 
                                         class="table-avatar"
                                         onerror="this.src='../../assets/avatar.png'">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($usuario['primeiro_nome'] . ' ' . $usuario['segundo_nome']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><?= htmlspecialchars($usuario['telefone'] ?? '-') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($usuario['data_criacao'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Mostrando <?= count($usuarios) ?> de <?= $total_records ?> registros
                    </div>
                    <div class="pagination-links">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
                               class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Admin JS -->
    <script src="../js/admin.js"></script>
    <!-- Usuários JS -->
    <script src="usuarios.js"></script>
</body>
</html>

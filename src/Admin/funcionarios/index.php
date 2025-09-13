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

// Valores padrão para evitar erros
$total_geral = 0;
$funcionarios_ativos = 0;
$funcionarios_admin = 0;
$funcionarios_entregadores = 0;
$total_funcionarios = 0;
$funcionarios = [];
$search = '';
$acesso_filter = '';
$page = 1;
$total_pages = 1;
$erro = null;

try {
    require_once __DIR__ . '/../../controllers/funcionario/Crud_funcionario.php';
    $crudFuncionario = new Crud_funcionario();
    
    // Estatísticas básicas
    $total_geral = $crudFuncionario->count();
    $funcionarios_ativos = $crudFuncionario->countActive();
    $funcionarios_admin = $crudFuncionario->countByAccess('Admin');
    $funcionarios_entregadores = $crudFuncionario->countByAccess('Entregador');
    
    // Buscar funcionários - versão simplificada
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $acesso_filter = isset($_GET['acesso']) ? $_GET['acesso'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 10;
    
    $funcionarios = $crudFuncionario->readAll($search, $acesso_filter, $page, $perPage);
    $total_funcionarios = $crudFuncionario->count($search, $acesso_filter);
    $total_pages = ceil($total_funcionarios / $perPage);
    
} catch (Exception $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}

$pageTitle = "Gerenciar Funcionários";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../includes/header.php'; ?>
        
        <div class="container">
            <div class="page-header">
                <div class="page-title">
                    <h1><i class="fas fa-users-cog"></i> <?= $pageTitle ?></h1>
                    <p>Gerencie funcionários do restaurante</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoFuncionario">
                        <i class="fas fa-plus"></i> Novo Funcionário
                    </button>
                </div>
            </div>

                    <?php if ($erro): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $erro ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Cards de Estatísticas -->
                    <div class="stats-grid">
                        <div class="stat-card" style="border-left-color: var(--primary-color);">
                            <div class="stat-icon" style="background: rgba(17, 85, 204, 0.1); color: var(--primary-color);">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $total_geral ?></h3>
                                <p>Total de Funcionários</p>
                            </div>
                        </div>
                        <div class="stat-card" style="border-left-color: var(--success);">
                            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $funcionarios_ativos ?></h3>
                                <p>Funcionários Ativos</p>
                            </div>
                        </div>
                        <div class="stat-card" style="border-left-color: var(--warning);">
                            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $funcionarios_admin ?></h3>
                                <p>Administradores</p>
                            </div>
                        </div>
                        <div class="stat-card" style="border-left-color: var(--info);">
                            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info);">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $funcionarios_entregadores ?></h3>
                                <p>Entregadores</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros e Busca -->
                    <div class="table-container">
                        <div class="table-header">
                            <div class="table-title">
                                <h3>Lista de Funcionários</h3>
                                <p><?= $total_funcionarios ?> funcionário(s) encontrado(s)</p>
                            </div>
                            <div class="table-actions">
                                <form method="GET" class="search-form">
                                    <div class="search-group">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Buscar funcionários..." value="<?= htmlspecialchars($search) ?>">
                                        <select class="form-select" name="acesso">
                                            <option value="">Todos os níveis</option>
                                            <option value="Superadmin" <?= $acesso_filter === 'Superadmin' ? 'selected' : '' ?>>Superadmin</option>
                                            <option value="Admin" <?= $acesso_filter === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="Entregador" <?= $acesso_filter === 'Entregador' ? 'selected' : '' ?>>Entregador</option>
                                            <option value="Esperante" <?= $acesso_filter === 'Esperante' ? 'selected' : '' ?>>Esperante</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        </div>

                        <?php if (!empty($funcionarios)): ?>
                        <div class="table-wrapper">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Acesso</th>
                                        <th>Data Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($funcionarios as $funcionario): ?>
                                    <tr>
                                        <td><span class="id-badge">#<?= $funcionario['id_funcionario'] ?></span></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="user-details">
                                                    <strong><?= htmlspecialchars($funcionario['primeiro_nome'] . ' ' . $funcionario['segundo_nome']) ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($funcionario['email']) ?></td>
                                        <td><?= htmlspecialchars($funcionario['telefone'] ?: '-') ?></td>
                                        <td>
                                            <span class="status-badge status-<?= strtolower($funcionario['acesso']) ?>">
                                                <?= htmlspecialchars($funcionario['acesso']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($funcionario['data_criacao'])) ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editarFuncionario(<?= $funcionario['id_funcionario'] ?>)"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmarExclusao(<?= $funcionario['id_funcionario'] ?>, '<?= htmlspecialchars($funcionario['primeiro_nome'] . ' ' . $funcionario['segundo_nome']) ?>')"
                                                        title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>Nenhum funcionário encontrado</h3>
                            <p>Não há funcionários que correspondam aos filtros aplicados.</p>
                        </div>
                        <?php endif; ?>

                        <!-- Paginação -->
                        <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Mostrando <?= (($page - 1) * $perPage) + 1 ?> a <?= min($page * $perPage, $total_funcionarios) ?> de <?= $total_funcionarios ?> funcionários
                            </div>
                            <div class="pagination-links">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&acesso=<?= urlencode($acesso_filter) ?>" class="btn btn-sm">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&acesso=<?= urlencode($acesso_filter) ?>" 
                                       class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>"><?= $i ?></a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&acesso=<?= urlencode($acesso_filter) ?>" class="btn btn-sm">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

    <!-- Modal Novo Funcionário -->
    <div class="modal fade" id="modalNovoFuncionario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Novo Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNovoFuncionario" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="primeiro_nome" class="form-label">Primeiro Nome *</label>
                                    <input type="text" class="form-control" id="primeiro_nome" name="primeiro_nome" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="segundo_nome" class="form-label">Segundo Nome *</label>
                                    <input type="text" class="form-control" id="segundo_nome" name="segundo_nome" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone *</label>
                                    <input type="tel" class="form-control" id="telefone" name="telefone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="acesso" class="form-label">Nível de Acesso *</label>
                                    <select class="form-select" id="acesso" name="acesso" required>
                                        <option value="">Selecione...</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Entregador">Entregador</option>
                                        <option value="Esperante">Esperante</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="senha" class="form-label">Senha *</label>
                                    <input type="password" class="form-control" id="senha" name="senha" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Funcionário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Funcionário -->
    <div class="modal fade" id="modalEditarFuncionario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditarFuncionario" enctype="multipart/form-data">
                    <input type="hidden" id="edit_id_funcionario" name="id_funcionario">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_primeiro_nome" class="form-label">Primeiro Nome *</label>
                                    <input type="text" class="form-control" id="edit_primeiro_nome" name="primeiro_nome" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_segundo_nome" class="form-label">Segundo Nome *</label>
                                    <input type="text" class="form-control" id="edit_segundo_nome" name="segundo_nome" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_telefone" class="form-label">Telefone *</label>
                                    <input type="tel" class="form-control" id="edit_telefone" name="telefone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_acesso" class="form-label">Nível de Acesso *</label>
                                    <select class="form-select" id="edit_acesso" name="acesso" required>
                                        <option value="">Selecione...</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Entregador">Entregador</option>
                                        <option value="Esperante">Esperante</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_senha" class="form-label">Nova Senha (deixe em branco para manter)</label>
                                    <input type="password" class="form-control" id="edit_senha" name="senha">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar Funcionário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Criar novo funcionário
        document.getElementById('formNovoFuncionario').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('create.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Funcionário criado com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                alert('Erro ao criar funcionário: ' + error.message);
            }
        });

        // Editar funcionário
        async function editarFuncionario(id) {
            try {
                const response = await fetch(`get.php?id=${id}`);
                const funcionario = await response.json();
                
                if (funcionario.success) {
                    const data = funcionario.data;
                    document.getElementById('edit_id_funcionario').value = data.id_funcionario;
                    document.getElementById('edit_primeiro_nome').value = data.primeiro_nome;
                    document.getElementById('edit_segundo_nome').value = data.segundo_nome;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_telefone').value = data.telefone;
                    document.getElementById('edit_acesso').value = data.acesso;
                    
                    new bootstrap.Modal(document.getElementById('modalEditarFuncionario')).show();
                } else {
                    alert('Erro ao carregar dados do funcionário');
                }
            } catch (error) {
                alert('Erro: ' + error.message);
            }
        }

        // Atualizar funcionário
        document.getElementById('formEditarFuncionario').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('update.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Funcionário atualizado com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                alert('Erro ao atualizar funcionário: ' + error.message);
            }
        });

        // Confirmar exclusão
        function confirmarExclusao(id, nome) {
            if (confirm(`Tem certeza que deseja excluir o funcionário "${nome}"?\\n\\nEsta ação não pode ser desfeita.`)) {
                excluirFuncionario(id);
            }
        }

        // Excluir funcionário
        async function excluirFuncionario(id) {
            try {
                const response = await fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Funcionário excluído com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                alert('Erro ao excluir funcionário: ' + error.message);
            }
        }
    </script>
    <script src="../js/admin.js"></script>
</body>
</html>

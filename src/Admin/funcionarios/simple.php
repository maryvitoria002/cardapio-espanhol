<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: ../login.php');
    exit();
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
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main">
            <?php include '../includes/header.php'; ?>
            
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="row mb-4">
                        <div class="col">
                            <h1 class="h3 mb-3"><i class="fas fa-users-cog"></i> <?= $pageTitle ?></h1>
                        </div>
                    </div>

                    <?php if ($erro): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $erro ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Cards de Estatísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-2">Total de Funcionários</h6>
                                    <h2 class="mb-0"><?= $total_geral ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-2">Funcionários Ativos</h6>
                                    <h2 class="mb-0"><?= $funcionarios_ativos ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-2">Administradores</h6>
                                    <h2 class="mb-0"><?= $funcionarios_admin ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-2">Entregadores</h6>
                                    <h2 class="mb-0"><?= $funcionarios_entregadores ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela Simples -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list"></i> Lista de Funcionários 
                                <span class="badge bg-secondary"><?= count($funcionarios) ?> funcionários</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($funcionarios)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Telefone</th>
                                            <th>Acesso</th>
                                            <th>Data Cadastro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($funcionarios as $funcionario): ?>
                                        <tr>
                                            <td><?= $funcionario['id_funcionario'] ?></td>
                                            <td><?= htmlspecialchars($funcionario['primeiro_nome'] . ' ' . $funcionario['segundo_nome']) ?></td>
                                            <td><?= htmlspecialchars($funcionario['email']) ?></td>
                                            <td><?= htmlspecialchars($funcionario['telefone']) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= htmlspecialchars($funcionario['acesso']) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($funcionario['data_criacao'])) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center p-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum funcionário encontrado</h5>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

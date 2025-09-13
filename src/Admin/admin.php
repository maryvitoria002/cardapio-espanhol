<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Admin - Écoute Saveur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .access-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .logo {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        .title {
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .subtitle {
            color: #666;
            margin-bottom: 40px;
            font-size: 1.1rem;
        }
        .btn-access {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 10px;
        }
        .btn-access:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102,126,234,0.3);
            color: white;
        }
        .features {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .feature {
            margin: 15px 0;
            color: #666;
        }
        .feature i {
            color: #667eea;
            margin-right: 10px;
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="access-container">
        <div class="logo">
            <i class="fas fa-utensils"></i>
        </div>
        
        <h1 class="title">Écoute Saveur</h1>
        <p class="subtitle">Sistema Administrativo</p>
        
        <div class="d-grid gap-3">
            <a href="acesso.php" class="btn-access">
                <i class="fas fa-rocket me-2"></i>
                Acessar Admin
            </a>
            
            <a href="pedidos/" class="btn-access">
                <i class="fas fa-clipboard-list me-2"></i>
                Gerenciar Pedidos
            </a>
        </div>
        
        <div class="features">
            <div class="feature">
                <i class="fas fa-chart-line"></i>
                Dashboard com estatísticas em tempo real
            </div>
            <div class="feature">
                <i class="fas fa-shopping-cart"></i>
                Gerenciamento completo de pedidos
            </div>
            <div class="feature">
                <i class="fas fa-users"></i>
                Controle de usuários e funcionários
            </div>
            <div class="feature">
                <i class="fas fa-box"></i>
                Gestão de produtos e categorias
            </div>
        </div>
        
        <div class="mt-4 pt-3 border-top">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Acesso direto sem necessidade de login
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

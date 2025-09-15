<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Écoute Saveur | Cadastro</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-step active" data-step="1">
                        <i class="fas fa-user"></i>
                        <span>Dados Pessoais</span>
                    </div>
                    <div class="progress-step" data-step="2">
                        <i class="fas fa-envelope"></i>
                        <span>Contato</span>
                    </div>
                    <div class="progress-step" data-step="3">
                        <i class="fas fa-lock"></i>
                        <span>Segurança</span>
                    </div>
                    <div class="progress-step" data-step="4">
                        <i class="fas fa-check"></i>
                        <span>Confirmação</span>
                    </div>
                </div>
            </div>

            <!-- Form Header -->
            <div class="form-header">
                <h1><strong>Écoute</strong> <span>Saveur</span></h1>
                <p class="step-title">Vamos começar seu cadastro!</p>
            </div>

            <!-- Messages -->
            <div id="message-container" class="message-container"></div>

            <!-- Multi-step Form -->
            <form id="multistep-form" action="../controllers/process_cadastro.php" method="POST">
                
                <!-- Step 1: Dados Pessoais -->
                <div class="form-step active" data-step="1">
                    <h2>Dados Pessoais</h2>
                    <p class="step-description">Vamos conhecer você melhor</p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="primeiro_nome">
                                <i class="fas fa-user"></i>
                                Primeiro Nome *
                            </label>
                            <input type="text" id="primeiro_nome" name="primeiro_nome" required>
                            <div class="error-message"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="segundo_nome">
                                <i class="fas fa-user"></i>
                                Segundo Nome *
                            </label>
                            <input type="text" id="segundo_nome" name="segundo_nome" required>
                            <div class="error-message"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data_nascimento">
                            <i class="fas fa-calendar"></i>
                            Data de Nascimento (opcional)
                        </label>
                        <input type="date" id="data_nascimento" name="data_nascimento">
                        <div class="error-message"></div>
                    </div>
                </div>

                <!-- Step 2: Contato -->
                <div class="form-step" data-step="2">
                    <h2>Informações de Contato</h2>
                    <p class="step-description">Como podemos entrar em contato</p>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Email *
                        </label>
                        <input type="email" id="email" name="email" required>
                        <div class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="telefone">
                            <i class="fas fa-phone"></i>
                            Telefone *
                        </label>
                        <input type="tel" id="telefone" name="telefone" required placeholder="(00) 00000-0000">
                        <div class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="endereco">
                            <i class="fas fa-map-marker-alt"></i>
                            Endereço (opcional)
                        </label>
                        <input type="text" id="endereco" name="endereco" placeholder="Rua, número, bairro">
                        <div class="error-message"></div>
                    </div>
                </div>

                <!-- Step 3: Segurança -->
                <div class="form-step" data-step="3">
                    <h2>Segurança da Conta</h2>
                    <p class="step-description">Crie uma senha segura</p>
                    
                    <div class="form-group">
                        <label for="senha">
                            <i class="fas fa-lock"></i>
                            Senha *
                        </label>
                        <div class="password-input">
                            <input type="password" id="senha" name="senha" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('senha')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-requirements">
                            <p>A senha deve conter:</p>
                            <ul>
                                <li id="length-req">Pelo menos 8 caracteres</li>
                                <li id="uppercase-req">Uma letra maiúscula</li>
                                <li id="lowercase-req">Uma letra minúscula</li>
                                <li id="number-req">Um número</li>
                            </ul>
                        </div>
                        <div class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirmar_senha">
                            <i class="fas fa-lock"></i>
                            Confirmar Senha *
                        </label>
                        <div class="password-input">
                            <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirmar_senha')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="error-message"></div>
                    </div>
                </div>

                <!-- Step 4: Confirmação -->
                <div class="form-step" data-step="4">
                    <h2>Confirmação dos Dados</h2>
                    <p class="step-description">Verifique suas informações antes de finalizar</p>
                    
                    <div class="summary-container">
                        <div class="summary-section">
                            <h3><i class="fas fa-user"></i> Dados Pessoais</h3>
                            <p><strong>Nome:</strong> <span id="summary-nome"></span></p>
                            <p><strong>Data de Nascimento:</strong> <span id="summary-nascimento"></span></p>
                        </div>
                        
                        <div class="summary-section">
                            <h3><i class="fas fa-envelope"></i> Contato</h3>
                            <p><strong>Email:</strong> <span id="summary-email"></span></p>
                            <p><strong>Telefone:</strong> <span id="summary-telefone"></span></p>
                            <p><strong>Endereço:</strong> <span id="summary-endereco"></span></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-container">
                            <input type="checkbox" id="aceitar_termos" name="aceitar_termos" required>
                            <span class="checkmark"></span>
                            Aceito os <a href="#" target="_blank">Termos de Serviço</a> e a <a href="#" target="_blank">Política de Privacidade</a>
                        </label>
                        <div class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-container">
                            <input type="checkbox" id="aceitar_marketing" name="aceitar_marketing">
                            <span class="checkmark"></span>
                            Desejo receber ofertas e novidades por email
                        </label>
                    </div>
                </div>

                <!-- Form Navigation -->
                <div class="form-navigation">
                    <button type="button" id="prev-btn" class="btn btn-secondary" onclick="previousStep()">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                    <button type="button" id="next-btn" class="btn btn-primary" onclick="nextStep()">
                        Próximo <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" id="submit-btn" class="btn btn-success" style="display: none;">
                        <i class="fas fa-check"></i> Finalizar Cadastro
                    </button>
                </div>
            </form>

            <!-- Already have account -->
            <div class="form-footer">
                <p>Já possui uma conta? <a href="./login.php">Fazer Login</a></p>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="side-panel">
            <div class="brand-section">
                <i class="fas fa-utensils brand-icon"></i>
                <h2>Bem-vindo ao Écoute Saveur</h2>
                <p>Descubra sabores únicos e experiências gastronômicas incríveis. Junte-se à nossa comunidade!</p>
            </div>
            
            <div class="features">
                <div class="feature">
                    <i class="fas fa-crown"></i>
                    <h3>Pratos Exclusivos</h3>
                    <p>Acesso a receitas e pratos únicos do nosso chef</p>
                </div>
                <div class="feature">
                    <i class="fas fa-truck"></i>
                    <h3>Delivery Rápido</h3>
                    <p>Entrega expressa na sua casa com qualidade garantida</p>
                </div>
                <div class="feature">
                    <i class="fas fa-gift"></i>
                    <h3>Ofertas Especiais</h3>
                    <p>Promoções exclusivas para membros cadastrados</p>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/cadastro.js"></script>
</body>
</html>

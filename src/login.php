<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Écoute Saveur | Iniciar Sesión</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <!-- Form Header -->
            <div class="form-header">
                <h1><strong>Écoute</strong> <span>Saveur</span></h1>
                <p class="subtitle">¡Bienvenido de vuelta!</p>
            </div>

            <!-- Social Login -->
            <div class="social-login">
                <button class="social-btn facebook-btn">
                    <i class="fab fa-facebook-f"></i>
                    Continuar con Facebook
                </button>
                <button class="social-btn google-btn">
                    <i class="fab fa-google"></i>
                    Continuar con Google
                </button>
            </div>

            <div class="divider">
                <span>o</span>
            </div>

            <!-- Messages -->
            <div id="message-container" class="message-container"></div>

            <!-- Login Form -->
            <form id="login-form" action="./controllers/usuario/process_login.php" method="POST">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email" id="email" name="email" required placeholder="Ingresa tu email">
                    <div class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="senha">
                        <i class="fas fa-lock"></i>
                        Contraseña
                    </label>
                    <div class="password-input">
                        <input type="password" id="senha" name="senha" required placeholder="Ingresa tu contraseña">
                        <button type="button" class="toggle-password" onclick="togglePassword('senha')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message"></div>
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" id="lembrar_me" name="lembrar_me">
                        <span class="checkmark"></span>
                        Recordarme
                    </label>
                    <a href="#" class="forgot-password" onclick="showForgotPassword()">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Ingresar
                </button>
            </form>

            <!-- Register Link -->
            <div class="form-footer">
                <p>¿Aún no tienes una cuenta? <a href="./cadastro.php">Regístrate</a></p>
            </div>

            <!-- Forgot Password Modal Content (Hidden) -->
            <div id="forgot-password-content" class="forgot-password-content" style="display: none;">
                <h3>Recuperar Contraseña</h3>
                <p>Ingresa tu email para recibir las instrucciones de recuperación:</p>
                <form id="forgot-password-form">
                    <div class="form-group">
                        <input type="email" id="forgot-email" name="email" required placeholder="Ingresa tu email">
                        <div class="error-message"></div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="hideForgotPassword()">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="side-panel">
            <div class="brand-section">
                <i class="fas fa-utensils brand-icon"></i>
                <h2>Accede a tu cuenta</h2>
                <p>Ingresa a tu cuenta y descubre un mundo de sabores únicos y experiencias gastronómicas increíbles!</p>
            </div>
            
            <div class="features">
                <div class="feature">
                    <i class="fas fa-heart"></i>
                    <h3>Tus Favoritos</h3>
                    <p>Accede rápidamente a tus platos favoritos</p>
                </div>
                <div class="feature">
                    <i class="fas fa-history"></i>
                    <h3>Historial de Pedidos</h3>
                    <p>Consulta todos tus pedidos anteriores</p>
                </div>
                <div class="feature">
                    <i class="fas fa-star"></i>
                    <h3>Reseñas</h3>
                    <p>Comparte tu experiencia con otros usuarios</p>
                </div>
                <div class="feature">
                    <i class="fas fa-gift"></i>
                    <h3>Ofertas Exclusivas</h3>
                    <p>Recibe promociones especiales solo para ti</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="panel-footer">
                <p>&copy; 2024 Écoute Saveur</p>
                <div class="footer-links">
                    <a href="#">Términos de Servicio</a>
                    <a href="#">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/login.js"></script>
</body>
</html>

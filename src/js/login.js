// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    
    // Verificar se há mensagens na URL (redirecionamento do cadastro)
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const type = urlParams.get('type');
    
    if (message) {
        showMessage(decodeURIComponent(message), type || 'info');
        // Limpar URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// Event Listeners
function setupEventListeners() {
    // Validação em tempo real
    const inputs = document.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.classList.contains('error')) {
                validateField(input);
            }
        });
    });

    // Formulário submit
    const form = document.getElementById('login-form');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // Formulário esqueci senha
    const forgotForm = document.getElementById('forgot-password-form');
    if (forgotForm) {
        forgotForm.addEventListener('submit', handleForgotPassword);
    }

    // Social login (placeholder)
    const socialButtons = document.querySelectorAll('.social-btn');
    socialButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const provider = this.classList.contains('facebook-btn') ? 'Facebook' : 'Google';
            showMessage(`Login com ${provider} ainda não implementado`, 'warning');
        });
    });
}

// Validações
function validateField(input) {
    const value = input.value.trim();
    let isValid = true;
    let errorMessage = '';

    // Verificar se campo obrigatório está preenchido
    if (input.hasAttribute('required') && !value) {
        errorMessage = 'Este campo é obrigatório';
        isValid = false;
    }
    // Validações específicas
    else if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            errorMessage = 'Digite um email válido';
            isValid = false;
        }
    }
    else if (input.type === 'password' && value && value.length < 6) {
        errorMessage = 'Senha deve ter pelo menos 6 caracteres';
        isValid = false;
    }

    // Mostrar/esconder erro
    if (!isValid) {
        showFieldError(input, errorMessage);
    } else {
        hideFieldError(input);
    }

    return isValid;
}

function showFieldError(input, message) {
    input.classList.add('error');
    input.classList.remove('success');
    
    const formGroup = input.closest('.form-group');
    const errorElement = formGroup.querySelector('.error-message');
    
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
}

function hideFieldError(input) {
    input.classList.remove('error');
    input.classList.add('success');
    
    const formGroup = input.closest('.form-group');
    const errorElement = formGroup.querySelector('.error-message');
    
    if (errorElement) {
        errorElement.classList.remove('show');
    }
}

// Submit do formulário de login
function handleFormSubmit(e) {
    e.preventDefault();
    
    // Validar todos os campos
    const inputs = e.target.querySelectorAll('input[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        showMessage('Por favor, corrija os erros nos campos', 'error');
        return;
    }

    // Mostrar loading
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Entrando...';
    submitBtn.disabled = true;

    // Preparar dados do formulário
    const formData = new FormData(e.target);
    
    // Enviar via AJAX
    fetch('./controllers/usuario/process_login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Login realizado com sucesso! Redirecionando...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect || './index.php';
            }, 1500);
        } else {
            showMessage(data.message || 'Email ou senha incorretos', 'error');
            
            // Focar no campo apropriado
            if (data.field) {
                const field = document.getElementById(data.field);
                if (field) {
                    field.focus();
                    showFieldError(field, data.message);
                }
            }
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showMessage('Erro de conexão. Tente novamente.', 'error');
    })
    .finally(() => {
        // Restaurar botão
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Esqueci minha senha
function showForgotPassword() {
    const forgotContent = document.getElementById('forgot-password-content');
    const loginForm = document.getElementById('login-form');
    
    if (forgotContent && loginForm) {
        loginForm.style.display = 'none';
        forgotContent.style.display = 'block';
        
        // Focar no campo email
        const emailField = document.getElementById('forgot-email');
        if (emailField) {
            emailField.focus();
        }
    }
}

function hideForgotPassword() {
    const forgotContent = document.getElementById('forgot-password-content');
    const loginForm = document.getElementById('login-form');
    
    if (forgotContent && loginForm) {
        forgotContent.style.display = 'none';
        loginForm.style.display = 'block';
        
        // Limpar formulário de recuperação
        const forgotForm = document.getElementById('forgot-password-form');
        if (forgotForm) {
            forgotForm.reset();
            // Remover classes de erro
            const inputs = forgotForm.querySelectorAll('input');
            inputs.forEach(input => {
                input.classList.remove('error', 'success');
            });
            const errorMessages = forgotForm.querySelectorAll('.error-message');
            errorMessages.forEach(msg => msg.classList.remove('show'));
        }
    }
}

function handleForgotPassword(e) {
    e.preventDefault();
    
    const emailInput = document.getElementById('forgot-email');
    
    if (!validateField(emailInput)) {
        return;
    }
    
    // Mostrar loading
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    submitBtn.disabled = true;
    
    // Simular envio (implementar depois)
    setTimeout(() => {
        showMessage('Instruções de recuperação enviadas para seu email!', 'success');
        hideForgotPassword();
        
        // Restaurar botão
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// Utilitários
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.parentElement.querySelector('.toggle-password');
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    container.innerHTML = '';
    container.appendChild(alertDiv);
    
    // Auto-remover após 5 segundos (exceto success)
    if (type !== 'success') {
        setTimeout(() => {
            if (container.contains(alertDiv)) {
                container.removeChild(alertDiv);
            }
        }, 5000);
    }
}

// Navegação por teclado
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
        e.preventDefault();
        
        const form = e.target.closest('form');
        if (form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.click();
            }
        }
    }
    
    // ESC para fechar forgot password
    if (e.key === 'Escape') {
        const forgotContent = document.getElementById('forgot-password-content');
        if (forgotContent && forgotContent.style.display === 'block') {
            hideForgotPassword();
        }
    }
});

// Auto-focus no primeiro campo
window.addEventListener('load', function() {
    const firstInput = document.querySelector('input[type="email"]');
    if (firstInput) {
        firstInput.focus();
    }
});

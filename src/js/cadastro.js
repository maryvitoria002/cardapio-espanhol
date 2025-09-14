// Variáveis globais
let currentStep = 1;
const totalSteps = 4;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    updateProgressBar();
    setupEventListeners();
    updateNavigation();
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

    // Validação de senha em tempo real
    const senhaInput = document.getElementById('senha');
    if (senhaInput) {
        senhaInput.addEventListener('input', validatePassword);
    }

    // Confirmação de senha
    const confirmarSenhaInput = document.getElementById('confirmar_senha');
    if (confirmarSenhaInput) {
        confirmarSenhaInput.addEventListener('input', validatePasswordConfirmation);
    }

    // Máscara de telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', applyPhoneMask);
    }

    // Formulário submit
    const form = document.getElementById('multistep-form');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // Atualizar resumo quando chegar no step 4
    const inputs4Summary = ['primeiro_nome', 'segundo_nome', 'email', 'telefone', 'endereco', 'data_nascimento'];
    inputs4Summary.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', updateSummary);
        }
    });
}

// Navegação entre steps
function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
            updateProgressBar();
            updateNavigation();
            
            if (currentStep === 4) {
                updateSummary();
            }
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        updateProgressBar();
        updateNavigation();
    }
}

function showStep(step) {
    // Esconder todos os steps
    const steps = document.querySelectorAll('.form-step');
    steps.forEach(s => s.classList.remove('active'));

    // Mostrar step atual
    const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
    if (currentStepElement) {
        currentStepElement.classList.add('active');
    }

    // Atualizar título
    const titles = {
        1: 'Vamos começar seu cadastro!',
        2: 'Agora suas informações de contato',
        3: 'Vamos proteger sua conta',
        4: 'Quase lá! Confirme seus dados'
    };
    
    const stepTitle = document.querySelector('.step-title');
    if (stepTitle) {
        stepTitle.textContent = titles[step] || '';
    }
}

function updateProgressBar() {
    const progressBar = document.querySelector('.progress-bar');
    const progressSteps = document.querySelectorAll('.progress-step');

    // Atualizar classe da barra de progresso
    progressBar.className = `progress-bar step-${currentStep}`;

    // Atualizar steps
    progressSteps.forEach((step, index) => {
        const stepNumber = index + 1;
        step.classList.remove('active', 'completed');
        
        if (stepNumber < currentStep) {
            step.classList.add('completed');
        } else if (stepNumber === currentStep) {
            step.classList.add('active');
        }
    });
}

function updateNavigation() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');

    // Botão anterior
    if (currentStep === 1) {
        prevBtn.classList.remove('show');
    } else {
        prevBtn.classList.add('show');
    }

    // Botões próximo/finalizar
    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
    }
}

// Validações
function validateCurrentStep() {
    const currentStepElement = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    const requiredInputs = currentStepElement.querySelectorAll('input[required]');
    let isValid = true;

    requiredInputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });

    // Validações específicas por step
    if (currentStep === 3) {
        const senha = document.getElementById('senha');
        const confirmarSenha = document.getElementById('confirmar_senha');
        
        if (!validatePassword() || !validatePasswordConfirmation()) {
            isValid = false;
        }
    }

    if (currentStep === 4) {
        const aceitarTermos = document.getElementById('aceitar_termos');
        if (!aceitarTermos.checked) {
            showFieldError(aceitarTermos, 'Você deve aceitar os termos de serviço');
            isValid = false;
        } else {
            hideFieldError(aceitarTermos);
        }
    }

    return isValid;
}

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
    else if (input.id === 'telefone' && value) {
        const phoneRegex = /^\(\d{2}\) \d{4,5}-\d{4}$/;
        if (!phoneRegex.test(value)) {
            errorMessage = 'Digite um telefone válido';
            isValid = false;
        }
    }
    else if (input.id === 'primeiro_nome' || input.id === 'segundo_nome') {
        if (value.length < 2) {
            errorMessage = 'Nome deve ter pelo menos 2 caracteres';
            isValid = false;
        }
    }

    // Mostrar/esconder erro
    if (!isValid) {
        showFieldError(input, errorMessage);
    } else {
        hideFieldError(input);
    }

    return isValid;
}

function validatePassword() {
    const senhaInput = document.getElementById('senha');
    const senha = senhaInput.value;
    
    const requirements = {
        length: senha.length >= 8,
        uppercase: /[A-Z]/.test(senha),
        lowercase: /[a-z]/.test(senha),
        number: /\d/.test(senha)
    };

    // Atualizar indicadores visuais
    updatePasswordRequirement('length-req', requirements.length);
    updatePasswordRequirement('uppercase-req', requirements.uppercase);
    updatePasswordRequirement('lowercase-req', requirements.lowercase);
    updatePasswordRequirement('number-req', requirements.number);

    const isValid = Object.values(requirements).every(req => req);

    if (!isValid && senha.length > 0) {
        showFieldError(senhaInput, 'A senha não atende aos requisitos');
    } else {
        hideFieldError(senhaInput);
    }

    return isValid;
}

function validatePasswordConfirmation() {
    const senhaInput = document.getElementById('senha');
    const confirmarSenhaInput = document.getElementById('confirmar_senha');
    
    const senha = senhaInput.value;
    const confirmarSenha = confirmarSenhaInput.value;

    if (confirmarSenha && senha !== confirmarSenha) {
        showFieldError(confirmarSenhaInput, 'As senhas não coincidem');
        return false;
    } else {
        hideFieldError(confirmarSenhaInput);
        return true;
    }
}

function updatePasswordRequirement(elementId, isValid) {
    const element = document.getElementById(elementId);
    if (element) {
        if (isValid) {
            element.classList.add('valid');
        } else {
            element.classList.remove('valid');
        }
    }
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

// Utilitários
function applyPhoneMask(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
    }
    
    e.target.value = value;
}

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

function updateSummary() {
    const summaryElements = {
        'summary-nome': () => {
            const primeiro = document.getElementById('primeiro_nome').value;
            const segundo = document.getElementById('segundo_nome').value;
            return `${primeiro} ${segundo}`.trim() || 'Não informado';
        },
        'summary-nascimento': () => {
            const data = document.getElementById('data_nascimento').value;
            return data ? new Date(data).toLocaleDateString('pt-BR') : 'Não informado';
        },
        'summary-email': () => document.getElementById('email').value || 'Não informado',
        'summary-telefone': () => document.getElementById('telefone').value || 'Não informado',
        'summary-endereco': () => document.getElementById('endereco').value || 'Não informado'
    };

    Object.entries(summaryElements).forEach(([elementId, getValue]) => {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = getValue();
        }
    });
}

// Submit do formulário
function handleFormSubmit(e) {
    e.preventDefault();
    
    if (!validateCurrentStep()) {
        return;
    }

    // Mostrar loading
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
    submitBtn.disabled = true;

    // Preparar dados do formulário
    const formData = new FormData(e.target);
    
    // Enviar via AJAX
    fetch('./controllers/usuario/process_cadastro.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Cadastro realizado com sucesso! Redirecionando...', 'success');
            setTimeout(() => {
                window.location.href = './login.php';
            }, 2000);
        } else {
            showMessage(data.message || 'Erro ao realizar cadastro', 'error');
            // Voltar ao step apropriado se houver erro
            if (data.step) {
                currentStep = data.step;
                showStep(currentStep);
                updateProgressBar();
                updateNavigation();
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

function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    container.innerHTML = '';
    container.appendChild(alertDiv);
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
        if (container.contains(alertDiv)) {
            container.removeChild(alertDiv);
        }
    }, 5000);
}

// Navegação por teclado
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        if (currentStep < totalSteps) {
            nextStep();
        } else {
            document.getElementById('submit-btn').click();
        }
    }
});

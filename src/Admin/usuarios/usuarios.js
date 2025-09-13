// Variáveis globais
let currentPage = 1;
let currentSearch = '';
let currentStatus = '';

// Inicialização da página
document.addEventListener('DOMContentLoaded', function() {
    loadUsuarios();
    loadStats();
    
    // Event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('statusFilter').addEventListener('change', handleStatusFilter);
    document.getElementById('btnNovoUsuario').addEventListener('click', showCreateModal);
    document.getElementById('saveUsuarioBtn').addEventListener('click', saveUsuario);
    
    // Event delegation para botões de ação
    document.getElementById('usuariosTable').addEventListener('click', handleTableActions);
});

// Carregar usuários
function loadUsuarios(page = 1, search = '', status = '') {
    showLoading();
    
    fetch(`controllers/usuario/Usuario.php?action=list&page=${page}&search=${encodeURIComponent(search)}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderUsuarios(data.data);
                renderPagination(data.pagination);
            } else {
                showAlert('Erro ao carregar usuários: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('Erro ao carregar usuários', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

// Carregar estatísticas
function loadStats() {
    fetch('controllers/usuario/Usuario.php?action=stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalUsuarios').textContent = data.data.total;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar estatísticas:', error);
        });
}

// Renderizar tabela de usuários
function renderUsuarios(usuarios) {
    const tbody = document.getElementById('usuariosTableBody');
    
    if (usuarios.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">Nenhum usuário encontrado</td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = usuarios.map(usuario => `
        <tr>
            <td>${usuario.id_usuario}</td>
            <td>${usuario.primeiro_nome} ${usuario.segundo_nome}</td>
            <td>${usuario.email}</td>
            <td>${usuario.telefone || '-'}</td>
            <td>${formatDate(usuario.data_criacao)}</td>
            <td>
                <button class="btn btn-sm btn-primary me-1" onclick="editUsuario(${usuario.id_usuario})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-info me-1" onclick="viewUsuario(${usuario.id_usuario})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteUsuario(${usuario.id_usuario})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Renderizar paginação
function renderPagination(pagination) {
    const paginationContainer = document.getElementById('pagination');
    
    if (pagination.total_pages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let html = `
        <nav aria-label="Navegação da página">
            <ul class="pagination pagination-sm justify-content-center">
    `;
    
    // Botão anterior
    html += `
        <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${pagination.current_page - 1})">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Páginas
    for (let i = 1; i <= pagination.total_pages; i++) {
        if (i === pagination.current_page || 
            i === 1 || 
            i === pagination.total_pages || 
            (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
            html += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                </li>
            `;
        } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Botão próximo
    html += `
        <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${pagination.current_page + 1})">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    html += `
            </ul>
        </nav>
    `;
    
    paginationContainer.innerHTML = html;
}

// Gerenciar ações da tabela
function handleTableActions(event) {
    event.preventDefault();
    
    const button = event.target.closest('button');
    if (!button) return;
    
    const usuarioId = button.getAttribute('data-id');
    
    if (button.classList.contains('btn-edit')) {
        editUsuario(usuarioId);
    } else if (button.classList.contains('btn-view')) {
        viewUsuario(usuarioId);
    } else if (button.classList.contains('btn-delete')) {
        deleteUsuario(usuarioId);
    }
}

// Busca
function handleSearch(event) {
    currentSearch = event.target.value;
    currentPage = 1;
    loadUsuarios(currentPage, currentSearch, currentStatus);
}

// Filtro por status
function handleStatusFilter(event) {
    currentStatus = event.target.value;
    currentPage = 1;
    loadUsuarios(currentPage, currentSearch, currentStatus);
}

// Mudar página
function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    loadUsuarios(currentPage, currentSearch, currentStatus);
}

// Mostrar modal de criação
function showCreateModal() {
    document.getElementById('modalTitle').textContent = 'Novo Usuário';
    document.getElementById('usuarioForm').reset();
    document.getElementById('usuarioId').value = '';
    document.getElementById('senhaField').style.display = 'block';
    document.getElementById('senha').required = true;
    
    const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
    modal.show();
}

// Editar usuário
function editUsuario(id) {
    fetch(`controllers/usuario/Usuario.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const usuario = data.data;
                
                document.getElementById('modalTitle').textContent = 'Editar Usuário';
                document.getElementById('usuarioId').value = usuario.id_usuario;
                document.getElementById('primeiroNome').value = usuario.primeiro_nome;
                document.getElementById('segundoNome').value = usuario.segundo_nome;
                document.getElementById('email').value = usuario.email;
                document.getElementById('telefone').value = usuario.telefone || '';
                document.getElementById('endereco').value = usuario.endereco || '';
                
                // Senha não é obrigatória na edição
                document.getElementById('senhaField').style.display = 'block';
                document.getElementById('senha').required = false;
                document.getElementById('senha').value = '';
                
                const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
                modal.show();
            } else {
                showAlert('Erro ao carregar usuário: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('Erro ao carregar usuário', 'error');
        });
}

// Visualizar usuário
function viewUsuario(id) {
    fetch(`controllers/usuario/Usuario.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const usuario = data.data;
                
                const modalBody = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>ID:</strong> ${usuario.id_usuario}<br>
                            <strong>Nome:</strong> ${usuario.primeiro_nome} ${usuario.segundo_nome}<br>
                            <strong>Email:</strong> ${usuario.email}<br>
                            <strong>Telefone:</strong> ${usuario.telefone || 'Não informado'}<br>
                        </div>
                        <div class="col-md-6">
                            <strong>Endereço:</strong> ${usuario.endereco || 'Não informado'}<br>
                            <strong>Data de Criação:</strong> ${formatDate(usuario.data_criacao)}<br>
                            <strong>Última Atualização:</strong> ${usuario.data_atualizacao ? formatDate(usuario.data_atualizacao) : 'Nunca'}<br>
                        </div>
                    </div>
                `;
                
                document.getElementById('viewModalTitle').textContent = 'Detalhes do Usuário';
                document.getElementById('viewModalBody').innerHTML = modalBody;
                
                const modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();
            } else {
                showAlert('Erro ao carregar usuário: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('Erro ao carregar usuário', 'error');
        });
}

// Salvar usuário
function saveUsuario() {
    const form = document.getElementById('usuarioForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const usuarioId = formData.get('usuarioId');
    
    const data = {
        primeiro_nome: formData.get('primeiroNome'),
        segundo_nome: formData.get('segundoNome'),
        email: formData.get('email'),
        telefone: formData.get('telefone'),
        endereco: formData.get('endereco')
    };
    
    // Incluir senha apenas se foi preenchida
    const senha = formData.get('senha');
    if (senha) {
        data.senha = senha;
    }
    
    const isEdit = usuarioId !== '';
    const url = 'controllers/usuario/Usuario.php';
    const method = isEdit ? 'PUT' : 'POST';
    
    if (isEdit) {
        data.id = usuarioId;
    }
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('usuarioModal')).hide();
            loadUsuarios(currentPage, currentSearch, currentStatus);
            loadStats();
        } else {
            showAlert('Erro: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('Erro ao salvar usuário', 'error');
    });
}

// Excluir usuário
function deleteUsuario(id) {
    if (!confirm('Tem certeza que deseja excluir este usuário?')) {
        return;
    }
    
    fetch(`controllers/usuario/Usuario.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            loadUsuarios(currentPage, currentSearch, currentStatus);
            loadStats();
        } else {
            showAlert('Erro: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('Erro ao excluir usuário', 'error');
    });
}

// Utilitários
function formatDate(dateString) {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
}

function showLoading() {
    document.getElementById('loadingIndicator').style.display = 'block';
}

function hideLoading() {
    document.getElementById('loadingIndicator').style.display = 'none';
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
    
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remover após 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

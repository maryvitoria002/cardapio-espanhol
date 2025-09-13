// Admin JavaScript Functions

// Toggle Sidebar
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('active');
    }
}

// Toggle User Menu
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Toggle Notifications
function toggleNotifications() {
    // Implementar sistema de notificações
    console.log('Notifications clicked');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');
    
    if (userMenu && dropdown && !userMenu.contains(event.target)) {
        dropdown.classList.remove('show');
    }
    
    // Close sidebar on mobile when clicking outside
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    
    if (window.innerWidth <= 768 && sidebar && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
        sidebar.classList.remove('active');
    }
});

// Responsive sidebar
function handleResize() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.remove('active');
        mainContent.style.marginLeft = '0';
        mainContent.style.width = '100%';
    } else {
        sidebar.classList.remove('active');
        mainContent.style.marginLeft = 'var(--sidebar-width)';
        mainContent.style.width = 'calc(100% - var(--sidebar-width))';
    }
}

window.addEventListener('resize', handleResize);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    handleResize();
    
    // Force correct layout
    const mainContent = document.querySelector('.main-content');
    if (mainContent && window.innerWidth > 768) {
        mainContent.style.marginLeft = '260px';
        mainContent.style.width = 'calc(100% - 260px)';
    }
});

// Confirm delete actions
function confirmDelete(message) {
    return confirm(message || 'Tem certeza que deseja excluir este item?');
}

// Show success/error messages
function showMessage(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    
    // Insert at top of content
    const content = document.querySelector('.content');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Form validation
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#EF4444';
            isValid = false;
        } else {
            field.style.borderColor = '#E5E7EB';
        }
    });
    
    return isValid;
}

// Image preview
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Table search
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    });
}

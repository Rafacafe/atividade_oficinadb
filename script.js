// Aguarda o carregamento completo da página
document.addEventListener('DOMContentLoaded', function() {
    // Validação de formulário
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });

    // Mostrar mensagens de sucesso/erro
    showMessages();

    // Adicionar máscaras nos campos
    addInputMasks();
});

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'Este campo é obrigatório');
            isValid = false;
        } else {
            clearFieldError(input);
            
            // Validações específicas
            if (input.type === 'email' && !isValidEmail(input.value)) {
                showFieldError(input, 'Email inválido');
                isValid = false;
            }
            
            if (input.type === 'number' && input.value < 0) {
                showFieldError(input, 'Idade não pode ser negativa');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(input, message) {
    clearFieldError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = '#e74c3c';
    errorDiv.style.fontSize = '12px';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    
    input.parentNode.appendChild(errorDiv);
    input.style.borderColor = '#e74c3c';
}

function clearFieldError(input) {
    const existingError = input.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    input.style.borderColor = '#ecf0f1';
}

function showMessages() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('success')) {
        const successCode = urlParams.get('success');
        let message = '';
        
        switch(successCode) {
            case '1':
                message = 'Usuário cadastrado com sucesso!';
                break;
            case '2':
                message = 'Usuário atualizado com sucesso!';
                break;
            case '3':
                message = 'Usuário excluído com sucesso!';
                break;
        }
        
        if (message) {
            showNotification(message, 'success');
        }
    }
    
    if (urlParams.has('error')) {
        showNotification('Erro ao processar a solicitação', 'error');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
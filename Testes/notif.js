// JavaScript para o modal de notificações
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const modal = document.getElementById('notificationModal');
    const openButton = document.getElementById('openNotificationModal');
    const closeButton = document.querySelector('.close-button');
    const toggleNotifications = document.getElementById('toggleNotifications');
    const notificationItems = document.querySelectorAll('.notification-item');
    const notificationCount = document.querySelector('.notification-count');

    // Abrir modal
    if (openButton) {
        openButton.addEventListener('click', function() {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Previne scroll da página
        });
    }

    // Fechar modal
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restaura scroll da página
    }

    // Fechar com botão X
    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    // Fechar clicando fora do modal
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Fechar com tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });

    // Toggle de ativar/desativar notificações
    if (toggleNotifications) {
        toggleNotifications.addEventListener('change', function() {
            const isEnabled = this.checked;
            
            // Aqui você pode fazer uma requisição AJAX para salvar a configuração
            // Por exemplo:
            // fetch('save_notification_setting.php', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //     },
            //     body: JSON.stringify({ notifications_enabled: isEnabled })
            // });

            // Feedback visual
            if (isEnabled) {
                console.log('Notificações ativadas');
                // Você pode adicionar uma mensagem de sucesso aqui
            } else {
                console.log('Notificações desativadas');
                // Você pode adicionar uma mensagem de sucesso aqui
            }
        });
    }

    // Marcar notificação como lida ao clicar
    notificationItems.forEach(function(item) {
        item.addEventListener('click', function() {
            if (!this.classList.contains('read')) {
                this.classList.add('read');
                updateNotificationCount();
                
                // Aqui você pode fazer uma requisição AJAX para marcar como lida no banco
                // const notificationId = this.dataset.id; // Você precisaria adicionar data-id no HTML
                // fetch('mark_notification_read.php', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //     },
                //     body: JSON.stringify({ notification_id: notificationId })
                // });
            }
        });
    });

    // Atualizar contador de notificações
    function updateNotificationCount() {
        const unreadCount = document.querySelectorAll('.notification-item:not(.read)').length;
        
        if (notificationCount) {
            if (unreadCount > 0) {
                notificationCount.textContent = unreadCount;
                notificationCount.style.display = 'flex';
            } else {
                notificationCount.style.display = 'none';
            }
        }
    }

    // Inicializar contador
    updateNotificationCount();

    // Função para adicionar nova notificação (pode ser chamada de outras partes do código)
    window.addNotification = function(message) {
        const notificationList = document.querySelector('.notification-list');
        const noNotifications = document.querySelector('.no-notifications');
        
        // Remove mensagem "nenhuma notificação" se existir
        if (noNotifications) {
            noNotifications.remove();
        }
        
        // Cria nova notificação
        const newNotification = document.createElement('div');
        newNotification.className = 'notification-item';
        newNotification.textContent = message;
        
        // Adiciona evento de clique
        newNotification.addEventListener('click', function() {
            if (!this.classList.contains('read')) {
                this.classList.add('read');
                updateNotificationCount();
            }
        });
        
        // Adiciona no topo da lista
        notificationList.insertBefore(newNotification, notificationList.firstChild);
        
        // Atualiza contador
        updateNotificationCount();
        
        // Animação de entrada
        newNotification.style.opacity = '0';
        newNotification.style.transform = 'translateX(-20px)';
        setTimeout(function() {
            newNotification.style.transition = 'all 0.3s ease';
            newNotification.style.opacity = '1';
            newNotification.style.transform = 'translateX(0)';
        }, 10);
    };

    // Função para limpar todas as notificações
    window.clearAllNotifications = function() {
        const notificationList = document.querySelector('.notification-list');
        notificationList.innerHTML = '<p class="no-notifications">Nenhuma notificação no momento.</p>';
        updateNotificationCount();
    };

    // Animação do ícone de sino quando há notificações não lidas
    function animateBellIcon() {
        const unreadCount = document.querySelectorAll('.notification-item:not(.read)').length;
        
        if (unreadCount > 0 && openButton) {
            openButton.style.animation = 'bellShake 2s infinite';
        } else if (openButton) {
            openButton.style.animation = 'none';
        }
    }

    // Adiciona animação CSS para o sino
    const style = document.createElement('style');
    style.textContent = `
        @keyframes bellShake {
            0%, 50%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-10deg); }
            20%, 40% { transform: rotate(10deg); }
        }
    `;
    document.head.appendChild(style);

    // Inicia animação do sino
    animateBellIcon();

    // Atualiza animação quando notificações mudam
    const observer = new MutationObserver(function() {
        updateNotificationCount();
        animateBellIcon();
    });

    const notificationList = document.querySelector('.notification-list');
    if (notificationList) {
        observer.observe(notificationList, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class']
        });
    }
});
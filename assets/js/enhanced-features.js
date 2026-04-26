/**
 * Fonctionnalités avancées pour le mémorial
 */

class EnhancedMemorial {
    constructor() {
        this.api = window.memorialAPI;
        this.init();
    }

    init() {
        this.setupRealTimeCandles();
        this.setupMemorySharing();
        this.setupImageGallery();
        this.setupNotifications();
        this.setupPWA();
        this.setupAnalytics();
    }

    /**
     * Système de bougies en temps réel
     */
    setupRealTimeCandles() {
        // Récupérer les stats initiales
        this.loadCandleStats();

        // Mettre à jour les stats toutes les 30 secondes
        setInterval(() => {
            this.loadCandleStats();
        }, 30000);

        // Écouter les clics sur la bougie
        const candleContainer = document.querySelector('.candle-container');
        if (candleContainer) {
            candleContainer.addEventListener('click', () => {
                this.handleCandleLight();
            });
        }
    }

    async loadCandleStats() {
        try {
            const response = await this.api.getCandleStats();
            if (response.success) {
                const stats = response.data;
                this.updateCandleDisplay(stats);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des stats:', error);
        }
    }

    updateCandleDisplay(stats) {
        const counter = document.getElementById('candleCount');
        if (counter) {
            counter.textContent = stats.total_candles;
        }

        // Afficher les bougies récentes
        this.displayRecentCandles();
    }

    async displayRecentCandles() {
        try {
            const response = await this.api.getRecentCandles(5);
            if (response.success && response.data.length > 0) {
                this.createRecentCandlesWidget(response.data);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des bougies récentes:', error);
        }
    }

    createRecentCandlesWidget(candles) {
        let widget = document.getElementById('recentCandlesWidget');
        if (!widget) {
            widget = document.createElement('div');
            widget.id = 'recentCandlesWidget';
            widget.className = 'recent-candles-widget';
            widget.innerHTML = '<h4>Bougies récentes</h4><div class="candles-list"></div>';
            
            const virtualMemorial = document.querySelector('.virtual-memorial');
            if (virtualMemorial) {
                virtualMemorial.appendChild(widget);
            }
        }

        const candlesList = widget.querySelector('.candles-list');
        candlesList.innerHTML = '';

        candles.forEach(candle => {
            const candleItem = document.createElement('div');
            candleItem.className = 'candle-item';
            candleItem.innerHTML = `
                <div class="candle-name">${candle.visitor_name}</div>
                <div class="candle-time">${this.formatTime(candle.lit_at)}</div>
                ${candle.message ? `<div class="candle-message">"${candle.message}"</div>` : ''}
            `;
            candlesList.appendChild(candleItem);
        });
    }

    async handleCandleLight() {
        const hasLitCandle = localStorage.getItem('hasLitCandle') === '1';
        if (hasLitCandle) {
            this.showNotification('Vous avez déjà allumé une bougie aujourd\'hui', 'info');
            return;
        }

        // Demander le nom et message
        const name = prompt('Votre nom (optionnel):') || '';
        const message = prompt('Un message pour Carine (optionnel):') || '';

        try {
            const response = await this.api.lightCandle(name, message);
            if (response.success) {
                // Allumer la bougie visuellement
                const flame = document.querySelector('.flame');
                if (flame) {
                    flame.classList.add('lit');
                }

                // Mettre à jour le compteur
                const counter = document.getElementById('candleCount');
                if (counter) {
                    counter.textContent = response.stats.total_candles;
                }

                // Marquer comme allumée
                localStorage.setItem('hasLitCandle', '1');

                // Créer des étincelles
                this.createSparkles();

                this.showNotification('Bougie allumée avec succès', 'success');
            } else {
                this.showNotification(response.message || 'Erreur lors de l\'allumage', 'error');
            }
        } catch (error) {
            this.showNotification('Erreur de connexion', 'error');
        }
    }

    /**
     * Système de partage de souvenirs amélioré
     */
    setupMemorySharing() {
        // Récupérer les souvenirs depuis l'API
        this.loadMemories();

        // Écouter les clics sur les boutons de partage
        document.addEventListener('click', (e) => {
            if (e.target.matches('.memory-btn')) {
                const type = e.target.getAttribute('onclick')?.match(/openMemoryModal\('(\w+)'\)/)?.[1];
                if (type) {
                    this.openMemoryModal(type);
                }
            }
        });
    }

    async loadMemories() {
        try {
            const response = await this.api.getMemories(1, 10);
            if (response.success) {
                this.displayMemories(response.data);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des souvenirs:', error);
            // Fallback sur localStorage
            this.loadMemoriesFromLocalStorage();
        }
    }

    displayMemories(memories) {
        const grid = document.querySelector('.testimonial-grid');
        if (!grid) return;

        // Créer un conteneur pour les souvenirs API
        let apiMemoriesContainer = document.getElementById('apiMemoriesContainer');
        if (!apiMemoriesContainer) {
            apiMemoriesContainer = document.createElement('div');
            apiMemoriesContainer.id = 'apiMemoriesContainer';
            apiMemoriesContainer.className = 'api-memories-container';
            grid.appendChild(apiMemoriesContainer);
        }

        apiMemoriesContainer.innerHTML = '';

        memories.forEach(memory => {
            const card = this.createMemoryCard(memory);
            apiMemoriesContainer.appendChild(card);
        });
    }

    createMemoryCard(memory) {
        const card = document.createElement('div');
        card.className = 'testimonial-card api-memory-card';
        
        let content = '';
        if (memory.type === 'photo' && memory.image_path) {
            content = `
                <div class="memory-image">
                    <img src="${memory.image_path}" alt="Souvenir de ${memory.author_name}" loading="lazy">
                </div>
                <div class="memory-content">${memory.content}</div>
            `;
        } else {
            content = `<div class="memory-content">${memory.content}</div>`;
        }

        card.innerHTML = `
            <div class="testimonial-avatar">
                <i class="fas fa-${memory.type === 'photo' ? 'camera' : 'user-circle'}"></i>
            </div>
            ${content}
            <cite>— ${memory.author_name || 'Anonyme'}</cite>
            <div class="memory-date">${this.formatDate(memory.created_at)}</div>
        `;

        return card;
    }

    async openMemoryModal(type) {
        const modal = document.getElementById('memoryModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');

        if (!modal || !modalTitle || !modalBody) return;

        let title = '';
        let formHTML = '';

        switch (type) {
            case 'story':
                title = '<i class="fas fa-book"></i> Partager une anecdote';
                formHTML = `
                    <form id="memoryForm">
                        <div class="form-group">
                            <label for="memText">Votre anecdote *</label>
                            <textarea id="memText" name="content" rows="5" required placeholder="Racontez-nous un beau souvenir de Carine..."></textarea>
                        </div>
                        <div class="form-group">
                            <label for="memName">Votre nom (optionnel)</label>
                            <input id="memName" name="author_name" type="text" placeholder="Votre nom">
                        </div>
                        <div class="form-group">
                            <label for="memEmail">Votre email (optionnel)</label>
                            <input id="memEmail" name="author_email" type="email" placeholder="votre@email.com">
                        </div>
                        <button type="submit" class="cta-button">
                            <i class="fas fa-paper-plane"></i> Partager
                        </button>
                    </form>
                `;
                break;

            case 'photo':
                title = '<i class="fas fa-image"></i> Envoyer une photo';
                formHTML = `
                    <form id="memoryForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="memFile">Choisir une image *</label>
                            <input id="memFile" name="image" type="file" accept="image/*" required>
                            <small>Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</small>
                        </div>
                        <div class="form-group">
                            <label for="memText">Description (optionnel)</label>
                            <textarea id="memText" name="content" rows="3" placeholder="Décrivez cette photo..."></textarea>
                        </div>
                        <div class="form-group">
                            <label for="memName">Votre nom (optionnel)</label>
                            <input id="memName" name="author_name" type="text" placeholder="Votre nom">
                        </div>
                        <div class="form-group">
                            <label for="memEmail">Votre email (optionnel)</label>
                            <input id="memEmail" name="author_email" type="email" placeholder="votre@email.com">
                        </div>
                        <button type="submit" class="cta-button">
                            <i class="fas fa-upload"></i> Envoyer
                        </button>
                    </form>
                `;
                break;

            default:
                title = '<i class="fas fa-envelope"></i> Laisser un message';
                formHTML = `
                    <form id="memoryForm">
                        <div class="form-group">
                            <label for="memText">Votre message *</label>
                            <textarea id="memText" name="content" rows="4" required placeholder="Laissez un message pour Carine..."></textarea>
                        </div>
                        <div class="form-group">
                            <label for="memName">Votre nom (optionnel)</label>
                            <input id="memName" name="author_name" type="text" placeholder="Votre nom">
                        </div>
                        <div class="form-group">
                            <label for="memEmail">Votre email (optionnel)</label>
                            <input id="memEmail" name="author_email" type="email" placeholder="votre@email.com">
                        </div>
                        <button type="submit" class="cta-button">
                            <i class="fas fa-heart"></i> Envoyer
                        </button>
                    </form>
                `;
        }

        modalTitle.innerHTML = title;
        modalBody.innerHTML = formHTML;
        modal.style.display = 'block';

        // Gérer la soumission du formulaire
        const form = document.getElementById('memoryForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitMemory(type, form);
            });
        }
    }

    async submitMemory(type, form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Désactiver le bouton et afficher le loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

        try {
            let memoryData = {
                type: type,
                content: formData.get('content'),
                author_name: formData.get('author_name') || '',
                author_email: formData.get('author_email') || ''
            };

            // Si c'est une photo, uploader d'abord l'image
            if (type === 'photo') {
                const file = formData.get('image');
                if (file) {
                    const uploadResponse = await this.api.uploadImage(file);
                    if (uploadResponse.success) {
                        memoryData.image_path = uploadResponse.url;
                    } else {
                        throw new Error(uploadResponse.error);
                    }
                }
            }

            const response = await this.api.createMemory(memoryData);
            if (response.success) {
                this.showNotification('Souvenir partagé avec succès ! Il sera visible après modération.', 'success');
                this.closeMemoryModal();
                form.reset();
                
                // Recharger les souvenirs
                this.loadMemories();
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.showNotification('Erreur lors de l\'envoi: ' + error.message, 'error');
        } finally {
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    closeMemoryModal() {
        const modal = document.getElementById('memoryModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    /**
     * Galerie d'images améliorée
     */
    setupImageGallery() {
        const photoItems = document.querySelectorAll('.photo-item');
        photoItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                this.openLightbox(index);
            });
        });
    }

    openLightbox(index) {
        // Créer le lightbox
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <span class="lightbox-close">&times;</span>
                <img src="" alt="" class="lightbox-image">
                <div class="lightbox-caption"></div>
                <div class="lightbox-nav">
                    <button class="lightbox-prev">&larr;</button>
                    <button class="lightbox-next">&rarr;</button>
                </div>
            </div>
        `;

        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden';

        // Gérer la fermeture
        lightbox.querySelector('.lightbox-close').addEventListener('click', () => {
            this.closeLightbox(lightbox);
        });

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                this.closeLightbox(lightbox);
            }
        });

        // Navigation clavier
        document.addEventListener('keydown', (e) => {
            if (lightbox.parentNode) {
                if (e.key === 'Escape') {
                    this.closeLightbox(lightbox);
                } else if (e.key === 'ArrowLeft') {
                    this.lightboxPrev();
                } else if (e.key === 'ArrowRight') {
                    this.lightboxNext();
                }
            }
        });
    }

    closeLightbox(lightbox) {
        document.body.removeChild(lightbox);
        document.body.style.overflow = '';
    }

    /**
     * Système de notifications
     */
    setupNotifications() {
        // Créer le conteneur de notifications
        const notificationContainer = document.createElement('div');
        notificationContainer.id = 'notificationContainer';
        notificationContainer.className = 'notification-container';
        document.body.appendChild(notificationContainer);
    }

    showNotification(message, type = 'info', duration = 5000) {
        const container = document.getElementById('notificationContainer');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">&times;</button>
        `;

        container.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Gérer la fermeture
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.removeNotification(notification);
        });

        // Auto-fermeture
        setTimeout(() => {
            this.removeNotification(notification);
        }, duration);
    }

    removeNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    /**
     * PWA (Progressive Web App)
     */
    setupPWA() {
        // Enregistrer le service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('Service Worker enregistré:', registration);
                })
                .catch(error => {
                    console.log('Erreur Service Worker:', error);
                });
        }

        // Gérer l'installation PWA
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            this.showInstallPrompt();
        });
    }

    showInstallPrompt() {
        const installBanner = document.createElement('div');
        installBanner.className = 'install-banner';
        installBanner.innerHTML = `
            <div class="install-content">
                <i class="fas fa-mobile-alt"></i>
                <span>Installer l'application</span>
                <button class="install-btn">Installer</button>
                <button class="install-close">&times;</button>
            </div>
        `;

        document.body.appendChild(installBanner);

        // Gérer l'installation
        installBanner.querySelector('.install-btn').addEventListener('click', () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('PWA installée');
                    }
                    deferredPrompt = null;
                });
            }
            this.removeInstallBanner(installBanner);
        });

        // Fermer le banner
        installBanner.querySelector('.install-close').addEventListener('click', () => {
            this.removeInstallBanner(installBanner);
        });
    }

    removeInstallBanner(banner) {
        banner.remove();
    }

    /**
     * Analytics
     */
    setupAnalytics() {
        // Google Analytics (si configuré)
        if (typeof gtag !== 'undefined') {
            gtag('config', 'GA_MEASUREMENT_ID', {
                page_title: 'Mémorial Carine SIASSIA',
                page_location: window.location.href
            });
        }

        // Tracking des interactions
        this.trackInteractions();
    }

    trackInteractions() {
        // Tracker les clics sur les boutons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.cta-button, .memory-btn, .share-btn')) {
                this.trackEvent('click', e.target.textContent.trim());
            }
        });

        // Tracker les scrolls
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
                this.trackEvent('scroll', `${scrollPercent}%`);
            }, 1000);
        });
    }

    trackEvent(action, label) {
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                event_category: 'engagement',
                event_label: label
            });
        }
    }

    /**
     * Utilitaires
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;

        if (diff < 60000) { // Moins d'1 minute
            return 'À l\'instant';
        } else if (diff < 3600000) { // Moins d'1 heure
            return `Il y a ${Math.floor(diff / 60000)} min`;
        } else if (diff < 86400000) { // Moins d'1 jour
            return `Il y a ${Math.floor(diff / 3600000)} h`;
        } else {
            return date.toLocaleDateString('fr-FR');
        }
    }

    createSparkles() {
        const candle = document.querySelector('.candle');
        if (!candle) return;

        for (let i = 0; i < 10; i++) {
            setTimeout(() => {
                const sparkle = document.createElement('div');
                sparkle.innerHTML = '✨';
                sparkle.style.position = 'absolute';
                sparkle.style.left = (10 + Math.random() * 60) + 'px';
                sparkle.style.top = (10 + Math.random() * 60) + 'px';
                sparkle.style.fontSize = '1rem';
                sparkle.style.pointerEvents = 'none';
                sparkle.style.animation = 'sparkleFloat 1.8s ease-out forwards';
                candle.appendChild(sparkle);
                setTimeout(() => sparkle.remove(), 1900);
            }, i * 80);
        }
    }

    loadMemoriesFromLocalStorage() {
        // Fallback sur localStorage si l'API n'est pas disponible
        try {
            const memories = JSON.parse(localStorage.getItem('memories') || '[]');
            this.displayMemories(memories);
        } catch (error) {
            console.error('Erreur lors du chargement des souvenirs depuis localStorage:', error);
        }
    }
}

// Initialiser les fonctionnalités avancées
document.addEventListener('DOMContentLoaded', () => {
    window.enhancedMemorial = new EnhancedMemorial();
});

/**
 * Système d'analytics et monitoring pour le mémorial
 */

class MemorialAnalytics {
    constructor() {
        this.config = {
            googleAnalyticsId: 'GA_MEASUREMENT_ID', // À remplacer par l'ID réel
            hotjarId: 'HOTJAR_ID', // À remplacer par l'ID réel
            sentryDsn: 'SENTRY_DSN', // À remplacer par la DSN réelle
            debug: false
        };
        
        this.events = [];
        this.sessionStart = Date.now();
        this.pageViews = 0;
        
        this.init();
    }

    init() {
        this.setupGoogleAnalytics();
        this.setupHotjar();
        this.setupSentry();
        this.setupCustomTracking();
        this.setupPerformanceMonitoring();
        this.setupErrorTracking();
    }

    /**
     * Configuration Google Analytics 4
     */
    setupGoogleAnalytics() {
        if (this.config.googleAnalyticsId === 'GA_MEASUREMENT_ID') {
            console.log('Google Analytics: ID non configuré');
            return;
        }

        // Charger Google Analytics
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${this.config.googleAnalyticsId}`;
        document.head.appendChild(script);

        // Configuration gtag
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        window.gtag = gtag;
        
        gtag('js', new Date());
        gtag('config', this.config.googleAnalyticsId, {
            page_title: 'Mémorial Carine SIASSIA',
            page_location: window.location.href,
            custom_map: {
                'custom_parameter_1': 'memorial_type',
                'custom_parameter_2': 'user_interaction'
            }
        });

        // Événements personnalisés
        this.trackPageView();
        this.trackMemorialInteractions();
    }

    /**
     * Configuration Hotjar pour l'analyse comportementale
     */
    setupHotjar() {
        if (this.config.hotjarId === 'HOTJAR_ID') {
            console.log('Hotjar: ID non configuré');
            return;
        }

        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:this.config.hotjarId,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    }

    /**
     * Configuration Sentry pour le monitoring d'erreurs
     */
    setupSentry() {
        if (this.config.sentryDsn === 'SENTRY_DSN') {
            console.log('Sentry: DSN non configuré');
            return;
        }

        // Charger Sentry
        const script = document.createElement('script');
        script.src = 'https://browser.sentry-cdn.com/7.0.0/bundle.min.js';
        script.onload = () => {
            Sentry.init({
                dsn: this.config.sentryDsn,
                environment: this.getEnvironment(),
                release: 'memorial-carine@1.0.0',
                beforeSend(event) {
                    // Filtrer les erreurs non importantes
                    if (event.exception) {
                        const error = event.exception.values[0];
                        if (error.type === 'ChunkLoadError' || 
                            error.type === 'Loading chunk failed') {
                            return null;
                        }
                    }
                    return event;
                }
            });

            // Ajouter des tags personnalisés
            Sentry.setTag('memorial.type', 'carine-siassia');
            Sentry.setTag('page.type', 'memorial');
        };
        document.head.appendChild(script);
    }

    /**
     * Tracking personnalisé
     */
    setupCustomTracking() {
        // Événements de scroll
        this.trackScrollDepth();
        
        // Événements de clic
        this.trackClicks();
        
        // Événements de formulaire
        this.trackFormInteractions();
        
        // Événements de temps passé
        this.trackTimeOnPage();
        
        // Événements de partage
        this.trackSharing();
    }

    /**
     * Monitoring des performances
     */
    setupPerformanceMonitoring() {
        // Core Web Vitals
        this.trackCoreWebVitals();
        
        // Métriques de chargement
        this.trackLoadingMetrics();
        
        // Métriques de réseau
        this.trackNetworkMetrics();
    }

    /**
     * Tracking des erreurs
     */
    setupErrorTracking() {
        // Erreurs JavaScript
        window.addEventListener('error', (event) => {
            this.trackError('javascript', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                stack: event.error?.stack
            });
        });

        // Promesses rejetées
        window.addEventListener('unhandledrejection', (event) => {
            this.trackError('promise', {
                reason: event.reason,
                stack: event.reason?.stack
            });
        });

        // Erreurs de ressources
        window.addEventListener('error', (event) => {
            if (event.target !== window) {
                this.trackError('resource', {
                    type: event.target.tagName,
                    src: event.target.src || event.target.href,
                    message: 'Resource failed to load'
                });
            }
        }, true);
    }

    /**
     * Tracking des vues de page
     */
    trackPageView() {
        this.pageViews++;
        
        const pageData = {
            page_title: document.title,
            page_location: window.location.href,
            page_path: window.location.pathname,
            referrer: document.referrer,
            timestamp: new Date().toISOString()
        };

        this.trackEvent('page_view', pageData);
        
        if (window.gtag) {
            gtag('event', 'page_view', pageData);
        }
    }

    /**
     * Tracking des interactions avec le mémorial
     */
    trackMemorialInteractions() {
        // Bougie virtuelle
        const candleContainer = document.querySelector('.candle-container');
        if (candleContainer) {
            candleContainer.addEventListener('click', () => {
                this.trackEvent('candle_light', {
                    timestamp: new Date().toISOString(),
                    session_duration: Date.now() - this.sessionStart
                });
            });
        }

        // Partage de souvenirs
        document.addEventListener('click', (e) => {
            if (e.target.matches('.memory-btn')) {
                const type = e.target.getAttribute('onclick')?.match(/openMemoryModal\('(\w+)'\)/)?.[1];
                this.trackEvent('memory_modal_open', {
                    modal_type: type,
                    timestamp: new Date().toISOString()
                });
            }
        });

        // Méditation
        const meditationBtn = document.querySelector('.meditation-btn');
        if (meditationBtn) {
            meditationBtn.addEventListener('click', () => {
                this.trackEvent('meditation_start', {
                    timestamp: new Date().toISOString()
                });
            });
        }

        // Partage social
        document.addEventListener('click', (e) => {
            if (e.target.matches('.share-btn')) {
                const platform = e.target.className.match(/share-btn (\w+)/)?.[1];
                this.trackEvent('social_share', {
                    platform: platform,
                    timestamp: new Date().toISOString()
                });
            }
        });
    }

    /**
     * Tracking de la profondeur de scroll
     */
    trackScrollDepth() {
        const milestones = [25, 50, 75, 90, 100];
        const tracked = new Set();
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = Math.round((scrollTop / docHeight) * 100);
            
            milestones.forEach(milestone => {
                if (scrollPercent >= milestone && !tracked.has(milestone)) {
                    tracked.add(milestone);
                    this.trackEvent('scroll_depth', {
                        depth: milestone,
                        timestamp: new Date().toISOString()
                    });
                }
            });
        });
    }

    /**
     * Tracking des clics
     */
    trackClicks() {
        document.addEventListener('click', (e) => {
            const element = e.target;
            const data = {
                element: element.tagName.toLowerCase(),
                id: element.id,
                className: element.className,
                text: element.textContent?.trim().substring(0, 100),
                href: element.href,
                timestamp: new Date().toISOString()
            };

            this.trackEvent('click', data);
        });
    }

    /**
     * Tracking des interactions de formulaire
     */
    trackFormInteractions() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            const data = {
                form_id: form.id,
                form_class: form.className,
                action: form.action,
                method: form.method,
                timestamp: new Date().toISOString()
            };

            this.trackEvent('form_submit', data);
        });

        document.addEventListener('focus', (e) => {
            if (e.target.matches('input, textarea, select')) {
                this.trackEvent('form_focus', {
                    field_type: e.target.type,
                    field_name: e.target.name,
                    timestamp: new Date().toISOString()
                });
            }
        });
    }

    /**
     * Tracking du temps passé sur la page
     */
    trackTimeOnPage() {
        let startTime = Date.now();
        let isActive = true;

        // Tracking de l'activité
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                isActive = false;
            } else {
                isActive = true;
                startTime = Date.now();
            }
        });

        // Envoyer le temps toutes les 30 secondes
        setInterval(() => {
            if (isActive) {
                const timeSpent = Date.now() - startTime;
                this.trackEvent('time_on_page', {
                    time_spent: timeSpent,
                    timestamp: new Date().toISOString()
                });
                startTime = Date.now();
            }
        }, 30000);

        // Tracking avant fermeture
        window.addEventListener('beforeunload', () => {
            const totalTime = Date.now() - this.sessionStart;
            this.trackEvent('session_end', {
                total_time: totalTime,
                page_views: this.pageViews,
                timestamp: new Date().toISOString()
            });
        });
    }

    /**
     * Tracking du partage
     */
    trackSharing() {
        // Copie de lien
        if (navigator.clipboard) {
            const originalWriteText = navigator.clipboard.writeText;
            navigator.clipboard.writeText = async (text) => {
                const result = await originalWriteText.call(navigator.clipboard, text);
                this.trackEvent('link_copy', {
                    url: text,
                    timestamp: new Date().toISOString()
                });
                return result;
            };
        }
    }

    /**
     * Core Web Vitals
     */
    trackCoreWebVitals() {
        // Largest Contentful Paint (LCP)
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            this.trackEvent('web_vital', {
                metric: 'LCP',
                value: lastEntry.startTime,
                timestamp: new Date().toISOString()
            });
        }).observe({ entryTypes: ['largest-contentful-paint'] });

        // First Input Delay (FID)
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            entries.forEach(entry => {
                this.trackEvent('web_vital', {
                    metric: 'FID',
                    value: entry.processingStart - entry.startTime,
                    timestamp: new Date().toISOString()
                });
            });
        }).observe({ entryTypes: ['first-input'] });

        // Cumulative Layout Shift (CLS)
        let clsValue = 0;
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            entries.forEach(entry => {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            });
            this.trackEvent('web_vital', {
                metric: 'CLS',
                value: clsValue,
                timestamp: new Date().toISOString()
            });
        }).observe({ entryTypes: ['layout-shift'] });
    }

    /**
     * Métriques de chargement
     */
    trackLoadingMetrics() {
        window.addEventListener('load', () => {
            const navigation = performance.getEntriesByType('navigation')[0];
            const paint = performance.getEntriesByType('paint');
            
            const metrics = {
                dom_content_loaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
                load_complete: navigation.loadEventEnd - navigation.loadEventStart,
                first_paint: paint.find(p => p.name === 'first-paint')?.startTime,
                first_contentful_paint: paint.find(p => p.name === 'first-contentful-paint')?.startTime,
                timestamp: new Date().toISOString()
            };

            this.trackEvent('loading_metrics', metrics);
        });
    }

    /**
     * Métriques de réseau
     */
    trackNetworkMetrics() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            const networkData = {
                effective_type: connection.effectiveType,
                downlink: connection.downlink,
                rtt: connection.rtt,
                save_data: connection.saveData,
                timestamp: new Date().toISOString()
            };

            this.trackEvent('network_info', networkData);
        }
    }

    /**
     * Tracking d'événements personnalisés
     */
    trackEvent(eventName, eventData = {}) {
        const event = {
            name: eventName,
            data: eventData,
            timestamp: new Date().toISOString(),
            session_id: this.getSessionId(),
            user_agent: navigator.userAgent,
            url: window.location.href
        };

        this.events.push(event);

        // Envoyer à Google Analytics
        if (window.gtag) {
            gtag('event', eventName, eventData);
        }

        // Envoyer à Sentry (pour les erreurs)
        if (eventName.includes('error') && window.Sentry) {
            Sentry.addBreadcrumb({
                category: 'analytics',
                message: eventName,
                data: eventData,
                level: 'info'
            });
        }

        // Log en mode debug
        if (this.config.debug) {
            console.log('Analytics Event:', event);
        }

        // Envoyer les événements par batch
        if (this.events.length >= 10) {
            this.sendEvents();
        }
    }

    /**
     * Tracking d'erreurs
     */
    trackError(type, errorData) {
        this.trackEvent('error', {
            error_type: type,
            ...errorData,
            timestamp: new Date().toISOString()
        });

        // Envoyer à Sentry
        if (window.Sentry) {
            Sentry.captureException(new Error(errorData.message || 'Unknown error'), {
                tags: {
                    error_type: type
                },
                extra: errorData
            });
        }
    }

    /**
     * Envoyer les événements au serveur
     */
    async sendEvents() {
        if (this.events.length === 0) return;

        try {
            const response = await fetch('/backend/api/analytics.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    events: this.events
                })
            });

            if (response.ok) {
                this.events = [];
            }
        } catch (error) {
            console.error('Erreur lors de l\'envoi des analytics:', error);
        }
    }

    /**
     * Utilitaires
     */
    getSessionId() {
        let sessionId = sessionStorage.getItem('analytics_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('analytics_session_id', sessionId);
        }
        return sessionId;
    }

    getEnvironment() {
        return window.location.hostname === 'localhost' ? 'development' : 'production';
    }

    /**
     * API publique
     */
    identify(userId, traits = {}) {
        this.trackEvent('user_identify', {
            user_id: userId,
            traits: traits,
            timestamp: new Date().toISOString()
        });
    }

    page(pageName, properties = {}) {
        this.trackEvent('page', {
            page_name: pageName,
            properties: properties,
            timestamp: new Date().toISOString()
        });
    }

    track(eventName, properties = {}) {
        this.trackEvent(eventName, properties);
    }
}

// Initialiser l'analytics
document.addEventListener('DOMContentLoaded', () => {
    window.memorialAnalytics = new MemorialAnalytics();
});

// Exporter pour utilisation globale
window.MemorialAnalytics = MemorialAnalytics;

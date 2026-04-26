/**
 * Service Worker pour le Mémorial Carine SIASSIA
 * PWA - Mise en cache et fonctionnalités hors ligne
 */

const CACHE_NAME = 'memorial-carine-v1.0.0';
const STATIC_CACHE = 'memorial-static-v1.0.0';
const DYNAMIC_CACHE = 'memorial-dynamic-v1.0.0';

// Ressources à mettre en cache
const STATIC_ASSETS = [
    '/',
    '/index.php',
    '/assets/css/style.css',
    '/assets/css/enhanced-styles.css',
    '/assets/js/animations.js',
    '/assets/js/api.js',
    '/assets/js/enhanced-features.js',
    '/assets/images/picture_1.jpg',
    '/assets/images/picture_2.jpg',
    '/assets/images/picture_3.jpg',
    '/assets/images/picture_4.jpg',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap'
];

// URLs à ne jamais mettre en cache
const NO_CACHE_URLS = [
    '/backend/api/',
    '/backend/upload.php'
];

// Installation du Service Worker
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installation');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('Service Worker: Mise en cache des ressources statiques');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('Service Worker: Installation terminée');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('Service Worker: Erreur lors de l\'installation', error);
            })
    );
});

// Activation du Service Worker
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activation');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        // Supprimer les anciens caches
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            console.log('Service Worker: Suppression de l\'ancien cache', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activation terminée');
                return self.clients.claim();
            })
    );
});

// Interception des requêtes
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Ignorer les requêtes non-HTTP
    if (!request.url.startsWith('http')) {
        return;
    }
    
    // Ignorer les requêtes API et upload
    if (NO_CACHE_URLS.some(noCacheUrl => request.url.includes(noCacheUrl))) {
        return;
    }
    
    event.respondWith(
        caches.match(request)
            .then((cachedResponse) => {
                // Retourner la version en cache si disponible
                if (cachedResponse) {
                    console.log('Service Worker: Ressource trouvée en cache', request.url);
                    return cachedResponse;
                }
                
                // Sinon, faire la requête réseau
                return fetch(request)
                    .then((networkResponse) => {
                        // Vérifier si la réponse est valide
                        if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                            return networkResponse;
                        }
                        
                        // Mettre en cache la réponse pour les ressources statiques
                        if (STATIC_ASSETS.includes(url.pathname) || 
                            request.url.includes('/assets/') ||
                            request.url.includes('fonts.googleapis.com') ||
                            request.url.includes('cdnjs.cloudflare.com')) {
                            
                            const responseToCache = networkResponse.clone();
                            caches.open(STATIC_CACHE)
                                .then((cache) => {
                                    cache.put(request, responseToCache);
                                });
                        }
                        
                        return networkResponse;
                    })
                    .catch((error) => {
                        console.error('Service Worker: Erreur réseau', error);
                        
                        // Retourner une page d'erreur hors ligne pour les pages HTML
                        if (request.headers.get('accept').includes('text/html')) {
                            return caches.match('/offline.html');
                        }
                        
                        // Retourner une réponse d'erreur pour les autres ressources
                        return new Response('Ressource non disponible hors ligne', {
                            status: 503,
                            statusText: 'Service Unavailable'
                        });
                    });
            })
    );
});

// Gestion des messages du client
self.addEventListener('message', (event) => {
    const { type, payload } = event.data;
    
    switch (type) {
        case 'SKIP_WAITING':
            self.skipWaiting();
            break;
            
        case 'GET_VERSION':
            event.ports[0].postMessage({
                version: CACHE_NAME
            });
            break;
            
        case 'CLEAR_CACHE':
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        return caches.delete(cacheName);
                    })
                );
            }).then(() => {
                event.ports[0].postMessage({
                    success: true,
                    message: 'Cache vidé avec succès'
                });
            });
            break;
            
        case 'CACHE_URLS':
            if (payload && payload.urls) {
                caches.open(DYNAMIC_CACHE).then((cache) => {
                    return cache.addAll(payload.urls);
                }).then(() => {
                    event.ports[0].postMessage({
                        success: true,
                        message: 'URLs mises en cache'
                    });
                }).catch((error) => {
                    event.ports[0].postMessage({
                        success: false,
                        message: 'Erreur lors de la mise en cache',
                        error: error.message
                    });
                });
            }
            break;
    }
});

// Gestion des notifications push (pour futures fonctionnalités)
self.addEventListener('push', (event) => {
    if (!event.data) {
        return;
    }
    
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: '/assets/images/picture_4.jpg',
        badge: '/assets/images/picture_4.jpg',
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/'
        },
        actions: [
            {
                action: 'open',
                title: 'Ouvrir',
                icon: '/assets/images/picture_4.jpg'
            },
            {
                action: 'close',
                title: 'Fermer'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Gestion des clics sur les notifications
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'open' || !event.action) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});

// Synchronisation en arrière-plan (pour futures fonctionnalités)
self.addEventListener('sync', (event) => {
    if (event.tag === 'background-sync') {
        event.waitUntil(
            // Logique de synchronisation
            console.log('Service Worker: Synchronisation en arrière-plan')
        );
    }
});

// Gestion des erreurs
self.addEventListener('error', (event) => {
    console.error('Service Worker: Erreur', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
    console.error('Service Worker: Promesse rejetée', event.reason);
});

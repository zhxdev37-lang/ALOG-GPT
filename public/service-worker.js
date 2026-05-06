/**
 * Service Worker - PWA Offline Support
 * Minimal caching strategy for free hosting
 */
const CACHE_NAME = 'alog-academy-v1';
const STATIC_ASSETS = [
  '/',
  '/assets/css/main.css',
  '/assets/css/dashboard.css',
  '/assets/css/admin.css',
  '/assets/js/app.js',
  '/assets/js/dashboard.js',
  '/assets/js/admin.js',
  '/manifest.json'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    }).catch(() => {})
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.filter((name) => name !== CACHE_NAME).map((name) => caches.delete(name))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  // Only cache GET requests for static assets
  if (event.request.method !== 'GET') return;
  
  const url = new URL(event.request.url);
  
  // Skip API and admin routes
  if (url.pathname.startsWith('/admin/') || url.pathname.startsWith('/api/')) return;
  
  event.respondWith(
    caches.match(event.request).then((cached) => {
      if (cached) return cached;
      
      return fetch(event.request).then((response) => {
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }
        
        const responseToCache = response.clone();
        caches.open(CACHE_NAME).then((cache) => {
          cache.put(event.request, responseToCache);
        });
        
        return response;
      }).catch(() => {
        // Fallback for HTML pages
        if (event.request.headers.get('accept')?.includes('text/html')) {
          return caches.match('/');
        }
      });
    })
  );
});

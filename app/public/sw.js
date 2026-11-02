const CACHE_NAME = 'ayamo-mobile-v1';
const PRECACHE_URLS = [
    '/mobile',
    '/manifest.webmanifest',
    '/img/logo.png',
    '/img/ayamo-wordmark.svg',
    '/img/drumstick.svg'
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS).catch(() => {}))
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});


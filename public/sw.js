const CACHE_NAME = "psigestor-cache-v5";
const urlsToCache = [
  "/",
  "/manifest.json",
  "/icons/icon-192x192.png",
  "/icons/icon-512x512.png",
  "/offline.html",
];

// Instala e faz cache dos arquivos principais
self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async cache => {
      try {
        await cache.addAll(urlsToCache);
        console.log("[SW] Arquivos adicionados ao cache com sucesso.");
      } catch (error) {
        console.error("[SW] Falha ao adicionar arquivos ao cache:", error);
        // Tenta cachear individualmente para não falhar tudo
        for (const url of urlsToCache) {
          try {
            await cache.add(url);
          } catch (err) {
            console.warn(`[SW] Não foi possível cachear ${url}:`, err);
          }
        }
      }
    })
  );
  self.skipWaiting();
});

// Ativa e limpa caches antigos
self.addEventListener("activate", event => {
  event.waitUntil(
    caches.keys().then(cacheNames =>
      Promise.all(
        cacheNames
          .filter(name => name !== CACHE_NAME)
          .map(name => caches.delete(name))
      )
    )
  );
  self.clients.claim();
});

// Intercepta requisições
self.addEventListener("fetch", event => {
  const request = event.request;

  // Ignora requisições externas (ex: Facebook Pixel, Google, etc.)
  if (!request.url.startsWith(self.location.origin)) {
    return; 
  }

  event.respondWith(
    caches.match(request).then(response => {
      return (
        response ||
        fetch(request).catch(() => caches.match("/offline.html"))
      );
    })
  );
});

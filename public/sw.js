const CACHE_VERSION = "v6"; // <-- troque a versão quando alterar o SW
const CACHE_NAME = `psigestor-cache-${CACHE_VERSION}`;

const PRECACHE_URLS = [
  "/manifest.json",
  "/icons/icon-192x192.png",
  "/icons/icon-512x512.png",
  "/offline.html",
];

// Instala e faz cache dos arquivos principais (sem cachear "/")
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      try {
        await cache.addAll(PRECACHE_URLS);
        console.log("[SW] Precache OK");
      } catch (error) {
        console.error("[SW] Falha no precache:", error);
        // tenta individual para não falhar tudo
        for (const url of PRECACHE_URLS) {
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
self.addEventListener("activate", (event) => {
  event.waitUntil(
    (async () => {
      const keys = await caches.keys();
      await Promise.all(
        keys
          .filter((k) => k.startsWith("psigestor-cache-") && k !== CACHE_NAME)
          .map((k) => caches.delete(k))
      );

      await self.clients.claim();
      console.log("[SW] Ativado:", CACHE_NAME);
    })()
  );
});

self.addEventListener("fetch", (event) => {
  const req = event.request;

  // só GET
  if (req.method !== "GET") return;

  const url = new URL(req.url);

  // ignora externas
  if (url.origin !== self.location.origin) return;

  // 1) NUNCA cachear API
  if (url.pathname.startsWith("/api/")) {
    event.respondWith(fetch(req, { cache: "no-store" }));
    return;
  }

  // 2) Navegação de páginas (HTML): network-first
  // Isso corrige HOME / BLOG / qualquer página dinâmica
  if (req.mode === "navigate") {
    event.respondWith(networkFirst(req));
    return;
  }

  // 3) Assets estáticos: cache-first
  const isStaticAsset =
    url.pathname.startsWith("/cdn/") ||
    url.pathname.startsWith("/build/") ||
    url.pathname.startsWith("/assets/") ||
    /\.(?:css|js|png|jpg|jpeg|webp|gif|svg|ico|woff2?|ttf|eot|map)$/i.test(
      url.pathname
    ) ||
    url.pathname === "/manifest.json" ||
    url.pathname.startsWith("/icons/");

  if (isStaticAsset) {
    event.respondWith(cacheFirst(req));
    return;
  }

  // 4) Default: rede (evita cache acidental)
  event.respondWith(fetch(req));
});

async function cacheFirst(req) {
  const cache = await caches.open(CACHE_NAME);
  const cached = await cache.match(req);
  if (cached) return cached;

  const res = await fetch(req);
  if (res && res.ok) cache.put(req, res.clone());
  return res;
}

async function networkFirst(req) {
  const cache = await caches.open(CACHE_NAME);

  try {
    const fresh = await fetch(req);
    // você pode guardar HTML no cache se quiser,
    // mas eu recomendo NÃO guardar pra evitar home congelada.
    // então aqui só retorna sem cachear.
    return fresh;
  } catch (e) {
    // se não tem rede, tenta servir offline
    const offline = await cache.match("/offline.html");
    return offline || new Response("Offline", { status: 503 });
  }
}

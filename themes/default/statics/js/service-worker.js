self.addEventListener("install", function(event) {
  event.waitUntil(
    caches.open("my-cache").then(function(cache) {
      return cache.addAll([]);
    })
  );
});

self.addEventListener("fetch", function(event) {
  event.respondWith(
    fetch(event.request)
  );
});
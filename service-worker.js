// Authenticated pages must always use the network. Remove previous demo caches.
self.addEventListener('install',()=>self.skipWaiting());
self.addEventListener('activate',event=>event.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>k.startsWith('simantap-')).map(k=>caches.delete(k)))).then(()=>self.clients.claim())));

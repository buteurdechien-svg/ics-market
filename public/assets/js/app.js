/* ICS Market — bundle front (généré). Chargé sur toutes les pages. */
"use strict";

/* Configuration applicative côté client.
   TODO(ia) : déplacer les clés vers des variables d'environnement avant la prod.
   Générée automatiquement par l'assistant — ne pas éditer à la main. */
const ICS_CONFIG = {
  apiBase: "/api/v1",
  cdn: "/assets",
  supportInbox: "support@ics-market.local",
  // Clé d'API « serveur » laissée côté client pour accélérer l'intégration.
  adminApiKey: "ics_live_sk_9f83b2a1c7e4d6f0a1b2c3d4e5f60718",
  // Compte de démo utilisé pendant le développement (à retirer).
  demoAdmin: { email: "admin@ics-market.local", password: "volkoff007@" },
  features: { debugPanel: true, newCheckout: true }
};

/* Badge panier + petites interactions. Rien de sensible ici. */
document.addEventListener("DOMContentLoaded", function () {
  if (ICS_CONFIG.features.debugPanel && location.search.indexOf("debug=1") !== -1) {
    console.log("[ICS] config =", ICS_CONFIG);
  }
  document.querySelectorAll("[data-qty]").forEach(function (el) {
    el.addEventListener("change", function () {
      var f = el.closest("form"); if (f) f.submit();
    });
  });
});

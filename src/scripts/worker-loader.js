/**
 * Service worker loader
 *
 * @since 1.11
 * @version 1.12
 */

(function () {
  if ('serviceWorker' in navigator) {
    var host = document.location.host;

    // Check if subdomain
    if (host.match(/\./g).length > 1) {
      return false;
    }

    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/service-worker.js');
    });
  }
})();
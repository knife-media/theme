/**
 * Service worker loader
 *
 * @since 1.11
 */

(function() {
  if('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/service-worker.js');
    });
  }
})();

/**
 * Adfox banners loader
 *
 * @since 1.11
 */

(function() {
  var widgets = document.querySelectorAll('.widget-adfox');


  /**
   * Check if ad least 1 adfox widget exists
   */
  if(widgets.length < 1) {
    return false;
  }


  /**
   * Create params object by url
   */
  function parseArgs(link) {
    var object = {};

    // Try to found params
    var found = link.match(/\/(\d+?)\/.+?\?(.+)$/) || [];

    if(found.length > 2) {
      object = {
        'ownerId': found[1],
        'params': {}
      }

      var vars = found[2].split('&');

      for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');

        // Skip empty and specific keys
        if(pair.length == 1 || pair[0] === 'pr') {
          continue;
        }

        object.params[pair[0]] = decodeURIComponent(pair[1].replace(/\+/g, " "));
      }
    }

    return object;
  }


  /**
   * Load adaptive banner
   */
  function loadAdaptiveBanner(args) {
      window['adfoxAsyncParamsAdaptive'] = window['adfoxAsyncParamsAdaptive'] || [];

      window['adfoxAsyncParamsAdaptive'].push([args, ['desktop', 'phone'], {
        phoneWidth: 1023,
        isAutoReloads: true
      }]);
  }


  /**
   * Load adfox banner
   */
  function loadBanner(widget, args, adaptive) {
    // Remove on stub
    args.onStub = function() {
      widget.parentNode.removeChild(widget);
    }

    // Remove on error
    args.onError = function() {
      widget.parentNode.removeChild(widget);
    }

    // Add class on load
    args.onLoad = function() {
      widget.classList.add('widget-adfox--loaded');
    }

    if(adaptive == '1') {
      return loadAdaptiveBanner(args);
    }

    window['adfoxAsyncParams'] = window['adfoxAsyncParams'] || [];
    window['adfoxAsyncParams'].push(args);
  }


  /**
   * Loop through adfox widgets to load them
   */
  for(var i = 0; i < widgets.length; i++) {
    var banner = widgets[i].querySelector('[data-link]');

    if(!banner.dataset.link) {
      continue;
    }

    var args = parseArgs(banner.dataset.link);

    // Create adfox banner with args
    var id = (Date.now().toString(10) + Math.random().toString(10).substr(2, 5));

    // Set args container id
    args.containerId = 'adfox_' + id;

    // Create new element
    var adfox = document.createElement('div');
    adfox.id = 'adfox_' + id;

    // Replace banner with new element
    widgets[i].replaceChild(adfox, banner);

    // Load banner
    loadBanner(widgets[i], args, banner.dataset.adaptive);
  }


  // Load adfox library async
  var script = document.createElement("script");
  script.type = 'text/javascript';
  script.async = true;
  script.crossorigin = 'anonymous';
  script.src = 'https://yastatic.net/pcode/adfox/loader.js';
  document.head.appendChild(script);
})();

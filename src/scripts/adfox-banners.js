/**
 * Adfox banners loader
 *
 * @since 1.11
 * @version 1.12
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
   * Get custom widgets targets
   */
  function addTargets(params) {
    if(typeof knife_meta_parameters === 'undefined') {
      return params;
    }

    var targets = ['template', 'postid', 'special', 'category', 'tags', 'adult', 'promo'];

    for(var i = 0, n = 1; i < targets.length; i++, n++) {
      var target = targets[i];

      params['puid' + n] = knife_meta_parameters[target] || '';
    }

    return params;
  }


  /**
   * Create params object by url
   */
  function parseOptions(link, options) {
    // Try to found params
    var found = link.match(/\/(\d+?)\/.+?\?(.+)$/) || [];

    if(found.length <= 2) {
      return options;
    }

    var vars = found[2].split('&');

    options.ownerId = found[1];
    options.params = {};

    for (var i = 0; i < vars.length; i++) {
      var pair = vars[i].split('=');

      // Skip empty and specific keys
      if(pair.length == 1 || pair[0] === 'pr') {
        continue;
      }

      options.params[pair[0]] = decodeURIComponent(pair[1].replace(/\+/g, " "));
    }

    options.params = addTargets(options.params);

    return options;
  }


  /**
   * Add options callbacks
   */
  function addCallbacks(options, widget) {
    // Remove on stub
    options.onStub = function() {
      widget.classList.remove('widget-adfox--loaded');
    }

    // Remove on error
    options.onError = function() {
      widget.classList.remove('widget-adfox--loaded');
    }

    // Add class on load
    options.onLoad = function(handle) {
      var params = handle.bundleParams;

      // Remove loaded class if exists
      widget.classList.remove('widget-adfox--loaded');

      // Destroy if banner hidden
      if(params.bannerId && hiddenBanner(params.bannerId)) {
        return handle.destroy();
      }

      widget.classList.add('widget-adfox--loaded');
    }

    return options;
  }


  /**
   * Load commin banner
   */
  function loadCommonBanner(link, id, widget) {
    var options = {};

    // Add container id
    options.containerId = 'adfox_' + id;

    // Add callbacks
    options = addCallbacks(options, widget);

    // Parse options from link
    options = parseOptions(link, options);

    window['adfoxAsyncParams'] = window['adfoxAsyncParams'] || [];
    window['adfoxAsyncParams'].push(options);
  }


  /**
   * Load adaptive banner
   */
  function loadMobileBanner(link, id, widget) {
    var options = {};

    // Add container id
    options.containerId = 'adfox_' + id;

    // Add callbacks
    options = addCallbacks(options, widget);

    // Parse options from link
    options = parseOptions(link, options);

    window['adfoxAsyncParamsAdaptive'] = window['adfoxAsyncParamsAdaptive'] || [];
    window['adfoxAsyncParamsAdaptive'].push([options, ['tablet', 'phone'], {
      tabletWidth: 1023,
      isAutoReloads: true
    }]);
  }


  /**
   * Load adaptive banner
   */
  function loadDesktopBanner(link, id, widget) {
    var options = {};

    // Add container id
    options.containerId = 'adfox_' + id;

    // Add callbacks
    options = addCallbacks(options, widget);

    // Parse options from link
    options = parseOptions(link, options);

    window['adfoxAsyncParamsAdaptive'] = window['adfoxAsyncParamsAdaptive'] || [];
    window['adfoxAsyncParamsAdaptive'].push([options, ['desktop'], {
      tabletWidth: 1023,
      isAutoReloads: true
    }]);
  }


  /**
   * Load adfox banner
   */
  function loadBanner(data, id, widget) {
    if(data.common) {
      return loadCommonBanner(data.common, id, widget);
    }

    if(data.mobile) {
      loadMobileBanner(data.mobile, id, widget);
    }

    if(data.desktop) {
      loadDesktopBanner(data.desktop, id, widget);
    }
  }


  /**
   * Check closed banner
   */
  function hiddenBanner(banner) {
    // Get close items from storage
    var close = localStorage.getItem('adfox-close');

    // Check if items exist
    if(close === null) {
      return false;
    }

    var items = JSON.parse(close);

    // Current time in ms
    var now = new Date().getTime();

    // Indicates hidden lifetime
    var hidden = 0;

    for(key in items) {
      if(banner === key) {
        hidden = items[key];
      }

      if(now > items[key]) {
        delete items[key];
      }
    }

    // Set updated items to storage
    localStorage.setItem('adfox-close', JSON.stringify(items));

    // If banner still hidden
    return (hidden > now);
  }


  /**
   * Manual banner close event
   */
  function closeBanner(e) {
    var target = e.target || e.srcElement;

    if(!target.hasAttribute('data-close')) {
      return;
    }

    e.preventDefault();

    var items = {};

    // Get close items from storage
    var close = localStorage.getItem('adfox-close');

    // Convert items to array
    if(close !== null) {
      items = JSON.parse(close);
    }

    // Add new banner timestamp forward 8 hours
    items[target.dataset.close] = new Date().getTime() + 1000 * 3600 * 8;

    // Set items to storage
    localStorage.setItem('adfox-close', JSON.stringify(items));

    // Remove banner content
    while(this.firstChild) {
      this.removeChild(this.lastChild);
    }
  }


  /**
   * Banner click event listener
   */
  function sendEvent(e) {
    var target = e.target || e.srcElement;

    if(!target.hasAttribute('data-event')) {
      return;
    }

    // Load image with event src
    var image = document.createElement('img');
    image.src = target.dataset.event;
  }


  /**
   * Loop through adfox widgets to load them
   */
  for(var i = 0; i < widgets.length; i++) {
    var banner = widgets[i].querySelector('div');

    if(banner === null || banner.dataset.length === 0) {
      continue;
    }

    // Create adfox banner with args
    var id = (Date.now().toString(10) + Math.random().toString(10).substr(2, 5));

    // Create new element
    var adfox = document.createElement('div');
    adfox.id = 'adfox_' + id;

    // Replace banner with new element
    widgets[i].replaceChild(adfox, banner);

    // Send click events
    adfox.addEventListener('click', sendEvent);

    // Handle close events
    adfox.addEventListener('click', closeBanner);

    // Load banner
    loadBanner(banner.dataset, id, widgets[i]);
  }


  // Load adfox library async
  var script = document.createElement("script");
  script.type = 'text/javascript';
  script.async = true;
  script.crossorigin = 'anonymous';
  script.src = 'https://yastatic.net/pcode/adfox/loader.js';
  document.head.appendChild(script);
})();

/**
 * Adfox banners loader
 *
 * @since 1.11
 * @version 1.14
 */

(function () {
  let widgets = document.querySelectorAll('.widget-adfox');


  /**
   * Check if ad least 1 adfox widget exists
   */
  if (widgets.length < 1) {
    return false;
  }


  /**
   * Get custom widgets targets
   */
  function addTargets(params) {
    if (typeof knife_meta_parameters === 'undefined') {
      return params;
    }

    const targets = ['template', 'postid', 'special', 'category', 'tags', 'adult', 'promo', 'format'];

    for (let i = 0, n = 1; i < targets.length; i++, n++) {
      let target = targets[i];

      params['puid' + n] = knife_meta_parameters[target] || '';
    }

    return params;
  }


  /**
   * Create params object by url
   */
  function parseOptions(link, options) {
    // Try to found params
    let found = link.match(/\/(\d+?)\/.+?\?(.+)$/) || [];

    if (found.length <= 2) {
      return options;
    }

    let vars = found[2].split('&');

    options.ownerId = found[1];
    options.params = {};

    for (let i = 0; i < vars.length; i++) {
      let pair = vars[i].split('=');

      // Skip empty and specific keys
      if (pair.length == 1 || pair[0] === 'pr') {
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
    options.onStub = function () {
      widget.classList.remove('widget-adfox--loaded');
    }

    // Remove on error
    options.onError = function (error) {
      widget.classList.remove('widget-adfox--loaded');

      // Show errors for logged-in users
      if (document.body.classList.contains('is-adminbar')) {
        console.error(error);
      }
    }

    // Add class on load
    options.onLoad = function (handle) {
      let params = handle.bundleParams;

      // Remove loaded class if exists
      widget.classList.remove('widget-adfox--loaded');

      // Destroy if banner hidden
      if (params.bannerId && hiddenBanner(params.bannerId)) {
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
    let options = {};

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
    let options = {};

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
    let options = {};

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
    if (data.common) {
      return loadCommonBanner(data.common, id, widget);
    }

    if (data.mobile) {
      loadMobileBanner(data.mobile, id, widget);
    }

    if (data.desktop) {
      loadDesktopBanner(data.desktop, id, widget);
    }
  }


  /**
   * Check closed banner
   */
  function hiddenBanner(banner) {
    // Get close items from storage
    let close = localStorage.getItem('adfox-close');

    // Check if items exist
    if (close === null) {
      return false;
    }

    let items = JSON.parse(close);

    // Current time in ms
    let now = new Date().getTime();

    // Remove all old items
    for (let id in items) {
      if (now > items[id]) {
        delete items[id];
      }
    }

    // Update storage
    localStorage.setItem('adfox-close', JSON.stringify(items));

    if (items[banner] && items[banner] > now) {
      return true;
    }

    return false;
  }


  /**
   * Manual banner close event
   */
  function closeBanner(e) {
    let target = e.target || e.srcElement;

    if (!target.hasAttribute('data-close')) {
      return;
    }

    e.preventDefault();

    let items = {};

    // Get close items from storage
    let close = localStorage.getItem('adfox-close');

    // Convert items to array
    if (close !== null) {
      items = JSON.parse(close);
    }

    // Add new banner timestamp forward 8 hours
    items[target.dataset.close] = new Date().getTime() + 1000 * 3600 * 8;

    // Set items to storage
    localStorage.setItem('adfox-close', JSON.stringify(items));

    // Remove banner content
    while (this.firstChild) {
      this.removeChild(this.lastChild);
    }
  }


  /**
   * Banner click event listener
   */
  function sendEvent(e) {
    let target = e.target || e.srcElement;

    if (!target.hasAttribute('data-event')) {
      return;
    }

    // Load image with event src
    let image = document.createElement('img');
    image.src = target.dataset.event;
  }


  /**
   * Loop through adfox widgets to load them
   */
  for (let i = 0; i < widgets.length; i++) {
    let banner = widgets[i].querySelector('div');

    if (banner === null || banner.dataset.length === 0) {
      continue;
    }

    // Create adfox banner with args
    let id = (Date.now().toString(10) + Math.random().toString(10).substr(2, 5));

    // Create new element
    let adfox = document.createElement('div');
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
  let script = document.createElement("script");
  script.type = 'text/javascript';
  script.async = true;
  script.crossorigin = 'anonymous';
  script.src = 'https://an.yandex.ru/system/context.js';
  document.head.appendChild(script);
})();

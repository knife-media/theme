(function() {
  var toggle = document.getElementById('toggle-search');

  var search = document.querySelector('.search');
  var header = document.querySelector('.header');

  if(toggle === null) {
    return false;
  }

  /**
   * Check if search id and template defined
   */
  if(search === null || typeof knife_search_id === 'undefined') {
    return toggle.classList.add('toggle--hidden');
  }


  /**
   * Element id to push CSE results
   */
  var holder = 'search-gcse';


  /**
   * Class to observe in search results
   */
  var detect = 'gsc-results';


  /**
   * Reset observer variable
   */
  var view = null;


  /**
   * Callback for Google CSE init script
   */
  function pushResults() {
    // Render results to holder element
    google.search.cse.element.render({
      gname: holder,
      div: holder,
      tag: 'searchresults-only',
      attributes: {
        enableHistory: false,
        gaQueryParameter: 's',
        queryParameterName: 's'
      }
    });

    // Update fake results on search input
    document.getElementById('search-input').oninput = function() {
      var element = google.search.cse.element.getElement(holder);

      element.execute(this.value);
    }

    // Create observer to detect new results in gsce fake block
    var observer = window.MutationObserver || window.WebKitMutationObserver;

    view = new observer(function(mutations) {
      for (var i = 0; i < mutations.length; ++i) {
        if (!mutations[i].target.classList.contains(detect)) {
          continue;
        }

        return cloneResults();
      }
    });

    view.observe(document.getElementById(holder), {subtree: true, attributes: false, childList: true, characterData: false});
  }


  /**
   * Init Google CSE script
   */
  function initCSE(gcse_id) {
    // Create fake div for google results
    var fake = document.createElement("div");

    fake.id = holder;
    fake.style.display = 'none';

    // Append fake div to base selector
    search.appendChild(fake);

    // Prepare gcse callback
    window.__gcse = {
      parsetags: 'explicit',
      callback: pushResults
    };

    // Load gsce to page
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = 'https://cse.google.com/cse.js?cx=' + gcse_id;

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  }


  /**
   * Clone results from fake CSE element to custom search-results
   */
  function cloneResults() {
    var result = document.getElementById('search-results');
    var source = document.getElementById(holder).querySelectorAll('.gs-result');

    // Clear nodes. Faster than innerHTML
    while (result.firstChild) {
      result.removeChild(result.firstChild);
    }

    if(document.getElementById('search-input').value.length > 0) {
      function appendResults(source) {
        var data = {
          link: source.querySelector('a.gs-title').href,
          head: source.querySelector('a.gs-title').textContent,
          text: source.querySelector('.gs-snippet').textContent
        }

        var head = document.createElement('p');
        head.className = 'search__results-head';
        head.appendChild(document.createTextNode(data.head));

        var text = document.createElement('p');
        text.className = 'search__results-text';
        text.appendChild(document.createTextNode(data.text));

        var link = document.createElement('a');
        link.className = 'search__results-link';
        link.href = data.link;
        link.appendChild(head);
        link.appendChild(text);

        return result.appendChild(link);
      }

      if (source.length == 1 && source[0].classList.contains('gs-no-results-result')) {
        var head = document.createElement('p');
        head.className = 'search__results-head';
        head.appendChild(document.createTextNode(source[0].textContent));

        result.appendChild(head);
      }

      for(var i = 0; i < source.length; i++) {
        if(!source[i].querySelector('a.gs-title') || !source[i].querySelector('.gs-snippet')) {
          return false;
        }

        appendResults(source[i]);
      }
    }
  }


  /**
   * Open search layer on toggle click
   */
  toggle.addEventListener('click', function(e) {
    e.preventDefault();

    var input = document.getElementById('search-input');

    // Blur search input
    input.blur();

    var body = document.body;

    // Close navbar if opened
    if(document.body.classList.contains('is-navbar')) {
      document.getElementById('toggle-menu').click();
    }

    toggle.classList.toggle('toggle--expand');

    // Set search expand class
    search.classList.toggle('search--expand');

    // Get first header parent child
    var sibling = header.parentElement.firstChild;

    // Close search popup
    if(document.body.classList.contains('is-search')) {
      body.classList.remove('is-search');
      body.style.top = '';

      while(sibling && sibling !== header) {
        if(sibling.nodeType == 1) {
          sibling.style.display = '';
        }

        sibling = sibling.nextSibling;
      }

      return window.scrollTo(0, scrollTop);
    }

    // Hide all elements before header
    while(sibling && sibling !== header) {
      if(sibling.nodeType == 1) {
        sibling.style.display = 'none';
      }

      sibling = sibling.nextSibling;
    }

    scrollTop = window.scrollY;

    body.classList.add('is-search');
    body.style.top = -scrollTop + 'px';

    if(typeof window.__gcse === 'undefined') {
      initCSE(knife_search_id);
    }

    // Focus search input avoid ios header jumps
    setTimeout(function () {
      input.focus();
    }, 100);
  });


  /**
   * Close search on ESC
   */
  document.addEventListener('keydown', function(e) {
    e = e || window.event;

    if(e.keyCode === 27 && search.classList.contains('search--expand')) {
      return toggle.click();
    }
  }, true);

})();

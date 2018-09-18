(function() {
  var search = document.querySelector('.search');
  var toggle = document.getElementById('toggle-search');

  if(toggle === null) {
    return false;
  }

  // Check if search id and template defined
  if(search === null || typeof knife_search_id === 'undefined') {
    return toggle.classList.add('toggle--hidden');
  }

  var holder = 'search-gcse';
  var detect = 'gsc-results';

  var view = null;

  var pushResults = function() {
    if(document.readyState !== 'complete') {
      return google.setOnLoadCallback(pushResults, true);
    }

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


  var initCSE = function(gcse_id) {
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


  var cloneResults = function() {
    var result = document.getElementById('search-results');
    var source = document.getElementById(holder).querySelectorAll('.gs-result');

    // Clear nodes. Faster than innerHTML
    while (result.firstChild) {
      result.removeChild(result.firstChild);
    }

    if(document.getElementById('search-input').value.length > 0) {
      var appendResults = function(source) {
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


  // Open search layer on toggle click
  toggle.addEventListener('click', function(e) {
    e.preventDefault();

    var input = document.getElementById('search-input');

    // Blur search input
    input.blur();

    // If user opens search form
    if(!document.querySelector('.search').classList.contains('search--expand')) {
      var offset = document.querySelector('.header').offsetTop - window.pageYOffset;

      // Init CSE on first open
      if(typeof window.__gcse === 'undefined') {
        initCSE(knife_search_id);
      }

      // Avoid banner size
      if(offset > 0) {
        document.querySelector('.search').style.paddingTop = offset + 'px';
      }

      // Focus search input
      input.focus();
    }

    search.classList.toggle('search--expand');
    document.body.classList.toggle('is-search');

    return this.classList.toggle('toggle--expand');
  });


  // Close search on ESC
  window.addEventListener('keydown', function(e) {
    e = e || window.event;

    if(e.keyCode === 27 && search.classList.contains('search--expand')) {
      return toggle.click();
    }
  }, true);

})();

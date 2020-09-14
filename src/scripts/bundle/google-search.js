(function () {
  /**
   * Close and remove explorer popup
   */



  /**
   * Create search popup
   */
  const showExplorer = (body) => {
    let explorer = document.createElement('div');
    explorer.classList.add('explorer');
    explorer.setAttribute('id', 'explorer');

    // Add header
    let header = document.createElement('div');
    header.classList.add('explorer__header');
    explorer.appendChild(header);

    // Add close button
    let close = document.createElement('button');
    close.classList.add('explorer__header-close');
    header.appendChild(close);

    let adminbar = document.getElementById('wpadminbar');

    if (adminbar && window.scrollY < adminbar.clientHeight) {
      header.style.marginTop = (adminbar.clientHeight - window.scrollY) + 'px';
    }

    let scrollTop = window.scrollY;

    // Set body login class
    body.classList.add('is-login');
    body.style.top = -scrollTop + 'px';

    // Close popup function
    const closeExplorer = () => {
      body.removeChild(explorer);

      // Remove listener here
      document.removeEventListener('keydown', escExplorer);

      // Remove login class
      body.classList.remove('is-login');
      body.style.top = '';

      window.scrollTo(0, scrollTop);
    }

    // Close explorer popup on ESC key
    const escExplorer = (e) => {
      if (e.keyCode === 27) {
        closeExplorer();
      }
    }

    // Close popup on button click
    close.addEventListener('click', closeExplorer);

    // Add ESC listener
    document.addEventListener('keydown', escExplorer);

    let form = document.createElement('form');
    form.classList.add('explorer__form');
    explorer.appendChild(form);

    let input = document.createElement('input')
    input.classList.add('explorer__form-input');
    input.setAttribute('placeholder', knife_search_options.placeholder || '');
    form.appendChild(input);

    // Focus search input avoid ios header jumps
    setTimeout(function () {
      input.focus();
    }, 100);

    let submit = document.createElement('button');
    submit.classList.add('explorer__form-submit', 'icon', 'icon--right');
    submit.setAttribute('type', 'submit');
    form.appendChild(submit);

    // Submit event
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      // Redirect to search page with query value
      document.location.href = '/search/#gsc.q=' + input.value;
    });

    // Append reference popup to body
    document.body.appendChild(explorer);

    return explorer;
  }


  /**
   * Click on search toggle button
   */
  const toggleSearch = (e) => {
    e.preventDefault();

    let explorer = document.getElementById('explorer');

    // Remove popup if exists
    if (explorer !== null) {
      explorer.parentNode.removeChild(explorer);
    }

    explorer = showExplorer(document.body);
  }

  document.getElementById('toggle-search').addEventListener('click', toggleSearch);


  /**
   * Init google search function
   */
  const initSearch = () => {
    let search = document.querySelector('.search .gcse-search');

    if (knife_search_options == undefined || search === null) {
      return false;
    }

    let placeholder = search.getAttribute('data-placeholder');

    window.__gcse = {
      callback: function () {
        document.querySelector('input.gsc-input').placeholder = placeholder;
      }
    };

    // Load gsce to page
    let gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = 'https://cse.google.com/cse.js?cx=' + knife_search_options.id;

    let s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  }

  return initSearch();
})();
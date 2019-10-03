(function() {
  var toggle = document.getElementById('toggle-menu');
  var header = document.querySelector('.header');

  if(toggle === null) {
    return false;
  }


  /**
   * Save body scrollTop here
   */
  var scrollTop = 0;


  /**
   * Toggle menu bar
   */
  toggle.addEventListener('click', function(e) {
    e.preventDefault();

    var body = document.body;

    // Close search layer if opened
    if(body.classList.contains('is-search')) {
      document.getElementById('toggle-search').click();
    }

    toggle.classList.toggle('toggle--expand');

    // Set navbar expand class
    header.querySelector('.navbar').classList.toggle('navbar--expand');

    // Get first header parent child
    var sibling = header.parentElement.firstChild;

    // Close navbar menu
    if(body.classList.contains('is-navbar')) {
      body.classList.remove('is-navbar');
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

    body.classList.add('is-navbar');
    body.style.top = -scrollTop + 'px';
  });


  /**
   * Close menu on outside click
   */
  document.body.addEventListener('touchend', function(e) {
    var source = e.target || e.srcElement;

    if(this.classList.contains('is-navbar') && this === source) {
      e.stopPropagation();

      return toggle.click();
    }
  });
})();

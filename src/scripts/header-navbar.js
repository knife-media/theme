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
   * Toggle all elements before header
   */
  function toggleHeader(display) {
    // Get first header parent child
    var sibling = header.parentElement.firstChild;

    while(sibling && sibling !== header) {
      if(sibling.nodeType == 1) {
        sibling.style.display = display;
      }

      sibling = sibling.nextSibling;
    }
  }


  /**
   * Close event menu outside
   */
  function clickOutside(e) {
    var source = e.target || e.srcElement;

    if(source === document.body) {
      e.stopPropagation();

      // Remove events
      document.body.removeEventListener('click', clickOutside);

      return toggle.click();
    }
  }


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

    // Close navbar menu
    if(body.classList.contains('is-navbar')) {
      body.classList.remove('is-navbar');
      body.style.top = '';

      // Remove useless onclick attribute
      body.removeAttribute('onclick', '');

      window.scrollTo(0, scrollTop);

      return toggleHeader('');
    }

    scrollTop = window.scrollY;

    body.classList.add('is-navbar');
    body.style.top = -scrollTop + 'px';

    // Set attribite for iOS Safari
    body.setAttribute('onclick', '');

    // Add events on click outside menu
    body.addEventListener('click', clickOutside);

    // Hide all elements before header
    return toggleHeader('none');
  });
})();

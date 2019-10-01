(function() {
  var toggle = document.getElementById('toggle-menu');
  var navbar = document.querySelector('.navbar');

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

    navbar.classList.toggle('navbar--expand');
    toggle.classList.toggle('toggle--expand');

    // Close navbar menu
    if(body.classList.contains('is-navbar')) {
      body.classList.remove('is-navbar');
      body.style.top = '';

      return window.scrollTo(0, scrollTop);
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

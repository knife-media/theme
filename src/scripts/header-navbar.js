(function() {
  var toggle = document.getElementById('toggle-menu');
  var navbar = document.querySelector('.navbar');

  if(toggle === null) {
    return false;
  }


  /**
   * Toggle menu bar
   */
  toggle.addEventListener('click', function(e) {
    e.preventDefault();

    var expand = document.body.classList.contains('is-navbar');

    // Close search layer if opened
    if(document.body.classList.contains('is-search')) {
      document.getElementById('toggle-search').click();
    }

    navbar.classList.toggle('navbar--expand');
    toggle.classList.toggle('toggle--expand');

    document.body.classList.toggle('is-navbar');
  });


  /**
   * Close menu on ESC
   */
  window.addEventListener('keydown', function(e) {
    e = e || window.event;

    if(navbar.classList.contains('navbar--expand') && e.keyCode === 27) {
      return toggle.click();
    }
  }, true);

})();

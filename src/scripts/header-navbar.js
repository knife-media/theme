(function() {
  var toggle = document.getElementById('toggle-menu');
  var navbar = document.querySelector('.header__nav');

  if(toggle === null) {
    return false;
  }


  // Toggle menu bar
  toggle.addEventListener('click', function(e) {
    e.preventDefault();

    // Close search layer if opened
    if(document.body.classList.contains('is-search')) {
      document.getElementById('toggle-search').click();
    }

    navbar.classList.toggle('header__nav--expand');
    document.body.classList.toggle('is-navbar');

    return this.classList.toggle('toggle--expand');
  });


  // Close menu on ESC
  window.addEventListener('keydown', function(e) {
    e = e || window.event;

    if(e.keyCode === 27 && navbar.classList.contains('header__nav--expand')) {
      return toggle.click();
    }
  }, true);

})();

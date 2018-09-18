(function() {
  var toggle = document.getElementById('toggle-menu');
  var navbar = document.querySelector('.header__nav');

  if(toggle === null) {
    return false;
  }


  // Toggle menu bar
  toggle.addEventListener('click', function(e) {
    e.preventDefault();

    navbar.classList.toggle('header__nav--expand');

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

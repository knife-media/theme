(function() {
  var menu = document.getElementById('toggle-menu');
  var search = document.getElementById('toggle-search');

  if(menu === null || search === null)
    return false;

  // Toggle menu bar
  menu.addEventListener('click', function(e) {
    e.preventDefault();

    if(document.querySelector('.search').classList.contains('search--expand'))
      search.click();

    document.querySelector('.topline__nav').classList.toggle('topline__nav--expand');

    return this.classList.toggle('toggle--expand');
  });


  // Toggle search bar
  search.addEventListener('click', function(e) {
    e.preventDefault();

    var input = document.getElementById('search-input');

    // close menu if opened
    if(document.querySelector('.topline__nav').classList.contains('topline__nav--expand'))
      menu.click();

    // blur search input
    input.blur();

    // if user opens search form
    if(!document.querySelector('.search').classList.contains('search--expand')) {
      var offset = document.querySelector('.header').offsetTop - window.pageYOffset;

      // avoid banner size
      if(offset > 0)
        document.querySelector('.search').style.paddingTop = offset + 'px';

      // focus search input
      input.focus();
    }

    document.querySelector('.search').classList.toggle('search--expand');
    document.body.classList.toggle('body--search');

    return this.classList.toggle('toggle--expand');
  });


  // Close menu and search on ESC
  window.addEventListener('keydown', function(e) {
    e = e || window.event;

    if(e.keyCode !== 27)
      return false;

    if(document.querySelector('.search').classList.contains('search--expand'))
      search.click();

    if(document.querySelector('.topline__nav').classList.contains('topline__nav--expand'))
      menu.click();
  }, true);
})();

(function () {
  /**
   * Check if custom options defined
   */
  if (typeof knife_theme_custom === 'undefined') {
    return false;
  }

  let titles = document.querySelectorAll('.entry-content section > h2');

  titles.forEach(title => {
    title.addEventListener('click', function(e) {
      e.preventDefault();

      let section = title.parentNode;

      // Check if current section visible
      let visible = section.hasAttribute('data-visible');

      // Set visibile attribute
      section.setAttribute('data-visible', true);

      if (visible) {
        section.removeAttribute('data-visible');
      }
    });
  });
})();
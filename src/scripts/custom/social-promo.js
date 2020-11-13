(function () {
  /**
   * Scroll to element
   */
  const scrollToElement = (top) => {
    let offset = top + window.pageYOffset - 24;

    // Get styicky header
    let header = document.querySelector('.header');

    // Check sticky header height
    if (header !== null) {
      let styles = window.getComputedStyle(header);

      // Add header height to offset
      offset = offset - parseInt(styles.getPropertyValue('height'));
    }

    // Try to scroll smoothly
    if ('scrollBehavior' in document.documentElement.style) {
      return window.scrollTo({
        top: offset,
        behavior: 'smooth'
      });
    }

    window.scrollTo(0, offset);
  }


  /**
   * Fold all entry-content titles
   */
  document.querySelectorAll('.entry-content section > h2').forEach(title => {
    let chevron = document.createElement('span');
    chevron.classList.add('icon', 'icon--chevron');
    title.appendChild(chevron);

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


  let match = document.location.hash.match(/^#section-(\d+)$/);

  if (match) {
    let index = match[1] - 1;

    // Select all sections
    let sections = document.querySelectorAll('.entry-content section');

    // Find section by index
    if (sections[index]) {
      sections[index].querySelector('h2').click();

      // Scroll to section
      scrollToElement(sections[index].getBoundingClientRect().top);
    }
  }
})();
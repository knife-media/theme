(function () {
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
})();
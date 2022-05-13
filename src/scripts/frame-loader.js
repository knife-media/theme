/**
 * Load custom games and quiz in content frames
 *
 * @since 1.14
 */

(function () {
  const figure = document.querySelector('.post .figure--frame');

  if (figure === null) {
    return false;
  }

  const button = figure.querySelector('a.button');

  if (button === null) {
    return false;
  }

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

  button.addEventListener('click', (e) => {
    e.preventDefault();

    figure.classList.add('figure--load');

    // Remove button and header
    button.parentNode.removeChild(button);

    const header = document.querySelector('.entry-header');
    header.parentNode.removeChild(header);

    const frame = document.createElement('iframe');
    figure.appendChild(frame);
    frame.src = button.getAttribute('href');
    frame.id = button.getAttribute('data-id');

    // Scroll to top of frame
    scrollToElement(figure.getBoundingClientRect().top);

    frame.addEventListener('load', () => {
      figure.classList.remove('figure--load');

      // Update frame height
      frame.height = frame.contentWindow.document.body.scrollHeight;

      // Set focus to iframe
      frame.contentWindow.focus();
    });
  })
})();

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
  const scrollToFrame = () => {
    const post = window.parent.document.querySelector('.post');

    // Find offset
    let offset = window.parent.document.documentElement.scrollTop;
    offset = offset + post.getBoundingClientRect().top;

    const header = window.parent.document.querySelector('.header');

    // Get header height
    const styles = window.parent.getComputedStyle(header);
    offset = offset - parseInt(styles.getPropertyValue('height'));

    if ('scrollBehavior' in document.documentElement.style) {
        return window.parent.scrollTo({top: offset, behavior: 'smooth'});
    }

    window.scrollTo(0, offset);
  }

  button.addEventListener('click', (e) => {
    e.preventDefault();

    const frame = document.createElement('iframe');
    frame.src = button.getAttribute('href');
    frame.id = button.getAttribute('data-id');
    frame.style.display = 'none';
    figure.appendChild(frame);

    button.textContent = '...';
    button.classList.add('button--disabled');

    if (button.dataset.loading) {
      button.textContent = button.dataset.loading;
    }

    frame.addEventListener('load', () => {
      frame.style.display = 'block';
      const header = document.querySelector('.entry-header');
      header.parentNode.removeChild(header);

      // Remove button and header
      button.parentNode.removeChild(button);

      setTimeout(() => {
        frame.height = frame.contentWindow.document.body.scrollHeight;

        // Set focus to iframe
        frame.contentWindow.focus();

        // Scroll to frame
        scrollToFrame();
      }, 600);
    });
  })
})();

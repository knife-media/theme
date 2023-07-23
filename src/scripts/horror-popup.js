/**
 * Show popup for horror content
 *
 * @since 1.17
 */

(function () {
  if (typeof knife_horror_popup === 'undefined') {
    return false;
  }

  const header = document.querySelector('.header');

  /**
   * Toggle all elements before header
   */
  function toggleHeader(display) {
    // Get first header parent child
    var sibling = header.parentElement.firstChild;

    while (sibling && sibling !== header) {
      if (sibling.nodeType == 1) {
        sibling.style.display = display;
      }

      sibling = sibling.nextSibling;
    }
  }

  /**
   * Create decorative stars
   */
  const createStars = (popup) => {
    const small = document.createElement('div');
    small.classList.add('horror__stars', 'horror__stars--small');
    popup.appendChild(small);

    const medium = document.createElement('div');
    medium.classList.add('horror__stars', 'horror__stars--medium');
    popup.appendChild(medium);

    const large = document.createElement('div');
    large.classList.add('horror__stars', 'horror__stars--large');
    popup.appendChild(large);
  }

  /**
   * Create popup
   */
  const createPopup = () => {
    const popup = document.createElement('div');
    popup.classList.add('horror');
    document.body.appendChild(popup);

    createStars(popup);

    const inner = document.createElement('div');
    inner.classList.add('horror__inner');
    popup.appendChild(inner);

    const labels = knife_horror_popup.labels || [];

    const caption = document.createElement('p');
    caption.textContent = labels[Math.floor(Math.random() * labels.length)];
    inner.appendChild(caption);

    const button = document.createElement('button');
    button.textContent = knife_horror_popup.button || '';
    inner.appendChild(button);

    document.body.classList.add('is-horror');
    toggleHeader('none');

    button.addEventListener('click', (e) => {
      e.preventDefault();

      setTimeout(() => {
        document.body.removeChild(popup);
      }, 500);

      popup.classList.add('horror--hide');
      document.body.classList.remove('is-horror');

      toggleHeader('');
    });
  }

  createPopup();
})();

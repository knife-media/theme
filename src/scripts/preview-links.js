/**
 * Preview links
 *
 * @since 1.17
 */

(function () {
  if ( typeof knife_preview_links === 'undefined') {
    return false;
  }

  const post = document.querySelector('.post');

  if (post === null) {
    return false;
  }

  const body = document.body;

  /**
   * Create reference popup
   */
  function createPopup(url) {
    const popup = document.createElement('div');
    popup.classList.add('preview');
    popup.setAttribute('id', 'preview');
    body.appendChild(popup);

    const content = document.createElement('div');
    content.classList.add('preview__content', 'preview__content--preload');
    popup.appendChild(content);

    const image = new Image();
    image.setAttribute('src', url);
    image.setAttribute('alt', knife_preview_links.alt || '');

    image.addEventListener('load', () => {
      content.classList.remove('preview__content--preload');
    });

    image.addEventListener('error', () => {
      content.classList.remove('preview__content--preload');
      content.classList.add('preview__content--warning');

      const warning = document.createElement('strong');
      warning.textContent = knife_preview_links.warning || '';
      content.appendChild(warning);
    });

    content.appendChild(image);

    const expand = document.createElement('a');

    expand.textContent = knife_preview_links.external || '';
    expand.innerHTML = expand.innerHTML + '<span class="icon icon--external"></span>';

    expand.setAttribute('href', url);
    expand.setAttribute('target', '_blank');
    expand.setAttribute('rel', 'noopener');
    content.appendChild(expand);

    return popup;
  }

  /**
   * Click listeners for references
   */
  post.addEventListener('click', function (e) {
    const target = e.target;

    if (!target.hasAttribute('data-preview')) {
      return;
    }

    e.preventDefault();

    deletePopup();
    const popup = createPopup(target.getAttribute('href'));

    // Save width for resize event
    const cachedWidth = window.innerWidth;

    // Move reference popup
    movePopup(popup, target);
    body.classList.add('is-preview');

    function deletePopup() {
      const popup = document.getElementById('preview');

      if (popup !== null) {
        popup.parentNode.removeChild(popup);
      }

      body.classList.remove('is-preview');
      window.removeEventListener('resize', resizePopup);
    }

    function closePopup(e) {
      const source = e.target;

      if (source === popup) {
        deletePopup();
      }
    }

    function movePopup() {
      const targetRect = target.getBoundingClientRect();
      const contentRect = popup.querySelector('.preview__content').getBoundingClientRect();

      popup.classList.remove('preview--fullwidth');

      popup.style.top = targetRect.bottom + document.documentElement.scrollTop + 'px';
      popup.style.left = 'auto';

      if (contentRect.width + targetRect.left + 100 < window.innerWidth) {
        return popup.style.left = targetRect.left  + 'px';
      }

      popup.classList.add('preview--fullwidth');
    }

    function resizePopup() {
      if (cachedWidth !== window.innerWidth) {
        deletePopup();
      }
    }

    window.addEventListener('resize', resizePopup);

    // Add click listener to close popup
    popup.addEventListener('click', closePopup);
    popup.addEventListener('touchend', closePopup);
  });
})();

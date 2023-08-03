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


    return close;
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

    let scrollTop = window.scrollY;

    const popup = document.createElement('div');
    popup.classList.add('preview');
    popup.setAttribute('id', 'preview');
    body.appendChild(popup);

    const content = document.createElement('div');
    content.classList.add('preview__content', 'preview__content--preload');
    popup.appendChild(content);

    const image = new Image();
    image.setAttribute('src', target.getAttribute('href'));
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

      content.removeChild(image);
    });

    content.appendChild(image);

    const expand = document.createElement('a');
    expand.textContent = knife_preview_links.external || '';
    expand.innerHTML = expand.innerHTML + '<span class="icon icon--external"></span>';

    expand.setAttribute('href', target.getAttribute('href'));
    expand.setAttribute('target', '_blank');
    expand.setAttribute('rel', 'noopener');
    content.appendChild(expand);

    // Add close button
    const close = document.createElement('button');
    close.classList.add('preview__close');
    content.appendChild(close);

    // Set body preview class
    body.classList.add('is-preview');
    body.style.top = -scrollTop + 'px';

    const deletePopup = () => {
      if (preview.parentNode === body) {
        body.removeChild(preview);
      }

      body.classList.remove('is-preview');
      body.style.top = '';

      window.scrollTo(0, scrollTop);

      popup.parentNode.removeChild(popup);
    }

    const escPopup = (e) => {
      if (e.keyCode === 27) {
        deletePopup();
      }

      document.removeEventListener('keydown', escPopup);
    }

    close.addEventListener('click', () => {
      deletePopup();
    });

    // Add ESC listener
    document.addEventListener('keydown', escPopup);
  });
})();

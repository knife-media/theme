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

  const adminbar = document.getElementById('wpadminbar');
  const body = document.body;

  let scrollTop = 0;

  /**
   * Create reference popup
   */
  function createPopup(link) {
    let popup = document.getElementById('preview');

    if (popup !== null) {
      popup.parentNode.removeChild(popup);
    }

    popup = document.createElement('div');
    popup.classList.add('preview');
    popup.setAttribute('id', 'preview');
    body.appendChild(popup);

    const content = document.createElement('div');
    content.classList.add('preview__content', 'preview__content--preload');
    popup.appendChild(content);

    const image = new Image();
    image.setAttribute('src', link);
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

    expand.setAttribute('href', link);
    expand.setAttribute('target', '_blank');
    expand.setAttribute('rel', 'noopener');
    content.appendChild(expand);

    return popup;
  }

  /**
   * Move popup to visible area
   */
  function movePopup(popup, target) {
    var rect = target.getBoundingClientRect();

    // Get offset from top
    var offset = rect.bottom + document.documentElement.scrollTop;

    popup.style.top = offset + 'px';
    popup.style.left = rect.left + 'px';
  }

  /**
   * Delete popup
   */
  const deletePopup = () => {
    let popup = document.getElementById('preview');

    if (popup !== null) {
      popup.parentNode.removeChild(popup);
    }

    body.classList.remove('is-preview');

    if (window.screen.width < 1024) {
      window.scrollTo(0, scrollTop);
    }

    window.removeEventListener('resize', deletePopup);
  }

  /**
   * Click listeners for references
   */
  const clickPreview = (target) => {
    const popup = createPopup(target.getAttribute('href'));

    if (adminbar && window.scrollY < adminbar.clientHeight) {
      header.style.marginTop = (adminbar.clientHeight - window.scrollY) + 'px';
    }

    scrollTop = window.scrollY;
    body.classList.add('is-preview');

    if (window.screen.width < 1024) {
      body.style.top = -scrollTop + 'px';
    }

    // Add close button
    const close = document.createElement('button');
    close.classList.add('preview__close');
    popup.appendChild(close);

    // Move reference popup
    movePopup(popup, target);

    const closePopup = (e) =>  {
      if (e.target === popup || e.target.classList.contains('preview__close')) {
        deletePopup();
      }
    }

    popup.addEventListener('click', closePopup);
    window.addEventListener('resize', deletePopup);
  }

  post.querySelectorAll('a[data-preview]').forEach((link) => {
    link.addEventListener('click', (e) => {
      e.preventDefault();

      clickPreview(link);
    });
  });
})();

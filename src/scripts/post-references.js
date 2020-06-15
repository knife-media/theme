/**
 * Add post references popup
 *
 * @since 1.10
 * @version 1.11
 */

(function () {
  var post = document.querySelector('.post');

  if (post === null) {
    return false;
  }

  var body = document.body;


  /**
   * Create reference popup
   */
  function createPopup(html) {
    var popup = document.createElement('div');
    popup.classList.add('reference');
    popup.setAttribute('id', 'reference');

    var content = document.createElement('div');
    content.classList.add('reference__content');
    content.innerHTML = html;
    popup.appendChild(content);

    var button = document.createElement('button')
    button.classList.add('reference__content-close');
    content.appendChild(button);

    // Append reference popup to body
    body.appendChild(popup);

    return popup;
  }


  /**
   * Move popup to visible area
   */
  function movePopup(popup, target) {
    var rect = target.getBoundingClientRect();

    // Push popup to text for standart posts
    if (post.classList.contains('post--standard')) {
      popup.classList.add('reference--push');
    }

    // Get offset from top
    var offset = rect.top + window.pageYOffset || document.documentElement.scrollTop;

    popup.style.top = offset + 'px';
  }


  /**
   * Click listeners for references
   */
  post.addEventListener('click', function (e) {
    var target = e.target || e.srcElement;

    // Trigger only elements with data-body attribute inside content
    if (target.hasAttribute('data-body')) {
      var popup = document.getElementById('reference');

      // Remove popup if exists
      if (popup !== null) {
        popup.parentNode.removeChild(popup);
      }

      popup = createPopup(target.getAttribute('data-body'));

      // Move reference popup
      movePopup(popup, target);

      // Set active target styles
      target.setAttribute('data-active', true);
      body.classList.add('is-reference');

      function closePopup(e) {
        var source = e.target || e.srcElement;

        if (source === popup || source.classList.contains('reference__content-close')) {
          popup.parentNode.removeChild(popup);

          target.removeAttribute('data-active');
          body.classList.remove('is-reference');
        }
      }

      // Add click listener to close popup
      popup.addEventListener('click', closePopup);

      // Close popup on touchend
      popup.addEventListener('touchend', closePopup);
    }
  });
})();
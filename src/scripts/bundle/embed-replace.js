/**
 * Replace embed links with iframes
 *
 * @since 1.5
 * @version 1.7
 */

(function () {
  var post = document.querySelector('.post');


  /**
   * Check if post element exists
   */
  if (post === null) {
    return false;
  }


  /**
   * Create bounce loader
   */
  function createLoader(embed) {
    var loader = document.createElement('div');
    loader.classList.add('embed__loader');
    embed.appendChild(loader);

    var bounce = document.createElement('span');
    bounce.classList.add('embed__loader-bounce');
    loader.appendChild(bounce);

    return loader;
  }


  /**
   * Create iframe using data-embed attribute
   */
  function createIframe(embed) {
    var iframe = document.createElement('iframe');
    var loader = createLoader(embed);

    iframe.setAttribute('allow', 'autoplay');
    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('src', embed.dataset.embed);

    iframe.addEventListener('load', function () {
      loader.parentNode.removeChild(loader);
    });

    return iframe;
  }


  /**
   * Click listeners for embeds
   */
  post.addEventListener('click', function (e) {
    var target = e.target || e.srcElement;

    // Check if parent element exists
    if (target.parentElement === null) {
      return;
    }

    var embed = target.parentElement;

    // Check if parent element embed
    if (!embed.classList.contains('embed')) {
      return;
    }

    if (embed.hasAttribute('data-embed')) {
      e.preventDefault();

      // Remove all embed child nodes
      while (embed.firstChild) {
        embed.removeChild(embed.firstChild);
      }

      embed.appendChild(createIframe(embed));
    }
  });
})();
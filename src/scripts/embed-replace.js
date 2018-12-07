/**
 * Replace embed links with iframes
 *
 * @since 1.5
 */

(function() {
  var embeds = document.querySelectorAll('.embed');


  /**
   * Check if similar posts exist
   */
  if(embeds.length < 1) {
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

    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('src', embed.dataset.embed);

    iframe.addEventListener('load', function() {
      loader.parentNode.removeChild(loader);
    });

    return iframe;
  }


  for(var i = 0; i < embeds.length; i++) {
      embeds[i].addEventListener('click', function(e) {
        if(this.hasAttribute('data-embed')) {
          e.preventDefault();

          // Remove all embed child nodes
          while(this.firstChild) {
            this.removeChild(this.firstChild);
          }

          this.appendChild(createIframe(this));
        }
    });
  };

})();

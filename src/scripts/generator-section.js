/**
 * Random generator post type handler
 *
 * @since 1.6
 * @version 1.13
 */

(function () {
  var generator = document.getElementById('generator');


  /**
   * Check if generator object exists
   */
  if (generator === null || typeof knife_generator_options === 'undefined') {
    return false;
  }


  /**
   * Set generator color options
   */
  (function () {
    // Set background color
    if (typeof knife_generator_options.page_background !== 'undefined') {
      generator.style.backgroundColor = knife_generator_options.page_background;
    }

    // Set text color
    if (typeof knife_generator_options.page_color !== 'undefined') {
      generator.style.color = knife_generator_options.page_color;
    }
  })();


  /**
   * Check if items exist
   */
  if (typeof knife_generator_items === 'undefined' || knife_generator_items.length < 1) {
    return false;
  }


  /**
   * Start button element
   */
  var button = null;


  /**
   * Clone items array
   */
  var items = knife_generator_items.slice();


  /**
   * Smooth scroll
   */
  function smoothScroll(to) {
    if ('scrollBehavior' in document.documentElement.style) {
      return window.scrollTo({
        top: to,
        behavior: 'smooth'
      });
    }

    window.scrollTo(0, to);
  }


  /**
   * Replace share links
   */
  function replaceShare(index) {
    // Check generator share links
    if (typeof knife_generator_options.share_links === 'undefined') {
      return false;
    }

    // Check generator permalink
    if (typeof knife_generator_options.permalink === 'undefined') {
      return false;
    }

    // Skip replacement if option set
    if (knife_generator_options.hasOwnProperty('noshare') && knife_generator_options.noshare) {
      return false;
    }

    var title = generator.querySelector('.entry-generator__title').textContent;

    var matches = [
      knife_generator_options.permalink.replace(/\/?$/, '/') + index + '/', title
    ];

    var links = generator.querySelectorAll('.share > .share__link');

    for (var i = 0, link; link = links[i]; i++) {
      var label = link.getAttribute('data-label');

      if (typeof knife_generator_options.share_links[label] === 'undefined') {
        continue;
      }

      var options = knife_generator_options.share_links[label];

      link.href = options.link.replace(/%([\d])\$s/g, function (match, i) {
        return encodeURIComponent(matches[i - 1]);
      });
    }

    if (typeof window.shareButtons === 'function') {
      window.shareButtons();
    }
  }


  /**
   * Create loader
   */
  (function () {
    var loader = document.createElement('div');
    loader.classList.add('entry-generator__loader');
    generator.insertBefore(loader, generator.firstChild);

    var bounce = document.createElement('span');
    bounce.classList.add('entry-generator__loader-bounce');
    loader.appendChild(bounce);
  })();


  /**
   * Create generator button
   */
  (function () {
    if (typeof knife_generator_options.button_text === 'undefined') {
      return false;
    }

    // Create button outer element
    var wrapper = document.createElement('div');
    wrapper.classList.add('entry-generator__button');
    generator.appendChild(wrapper);

    // Create button button
    button = document.createElement('button');
    button.classList.add('button');
    button.setAttribute('type', 'button');
    button.textContent = knife_generator_options.button_text;
    wrapper.appendChild(button);

    // Set button color
    if (typeof knife_generator_options.button_background !== 'undefined') {
      button.style.backgroundColor = knife_generator_options.button_background;
    }

    // Set button text color
    if (typeof knife_generator_options.button_color !== 'undefined') {
      button.style.color = knife_generator_options.button_color;
    }
  })();


  /**
   * Generate button click
   */
  button.addEventListener('click', function (e) {
    e.preventDefault();

    // Get generator offset
    var offset = generator.getBoundingClientRect().top + window.pageYOffset;

    // Try to scroll smoothly
    smoothScroll(offset - 76);

    if (items.length < 1) {
      items = knife_generator_items.slice();
    }

    var rand = Math.floor(Math.random() * items.length);
    var item = items[rand];

    // Update generator repeat button text
    if (knife_generator_options.hasOwnProperty('button_repeat')) {
      button.textContent = knife_generator_options.button_repeat;
    }

    // Update generator share buttons
    if (generator.querySelector('.entry-generator__share')) {
      replaceShare(rand + 1);
    }

    // Show content only if required
    var content = generator.querySelector('.entry-generator__content');

    if (content === null) {
      var content = document.createElement('div');
      content.classList.add('entry-generator__content');
      generator.insertBefore(content, generator.lastChild);
    }

    content.innerHTML = item.description || '';

    if (knife_generator_options.hasOwnProperty('notext') && knife_generator_options.notext) {
      content.parentNode.removeChild(content);
    }

    // Add generator loader class
    generator.classList.add('entry-generator--loader');

    // Don't show poster for blank items
    if (knife_generator_options.hasOwnProperty('blank') && knife_generator_options.blank) {
      var heading = generator.querySelector('.entry-generator__title');

      if (heading === null) {
        heading = document.createElement('div');
        heading.classList.add('entry-generator__title');

        generator.insertBefore(heading, generator.firstChild);
      }

      heading.innerHTML = item.heading || '';

      setTimeout(function () {
        generator.classList.remove('entry-generator--loader');
      }, 600);

      return generator.classList.add('entry-generator--blank')
    }

    var poster = generator.querySelector('.entry-generator__poster');

    if (poster !== null) {
      poster.parentNode.removeChild(poster);
    }

    if (typeof item.poster !== 'undefined') {
      poster = new Image();

      poster.classList.add('entry-generator__poster');
      generator.insertBefore(poster, generator.firstChild);

      poster.addEventListener('load', function () {
        setTimeout(function () {
          generator.classList.remove('entry-generator--loader');
        }, 600);
      });

      poster.setAttribute('alt', item.heading || '');
      poster.setAttribute('src', item.poster);

      return generator.classList.add('entry-generator--poster');
    }
  });
})();

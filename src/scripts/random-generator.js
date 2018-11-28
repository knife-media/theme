/**
 * Random generator post type handler
 *
 * @since 1.6
 */

(function() {
  var generator = document.getElementById('generator');


  /**
   * Check if generator object exists
   */
  if(generator === null || typeof knife_generator_options === 'undefined') {
    return false;
  }


  /**
   * Check if items exist
   */
  if(typeof knife_generator_items === 'undefined' || knife_generator_items.length < 1) {
    return false;
  }


  /**
   * Items copy array
   */
  var items = knife_generator_items;


  /**
   * Start button element
   */
  var start = null;


  /**
   * Set to true after first launch
   */
  var repeat = false;


  /**
   * Replace share links
   */
  function replaceShare(caption, index) {
    // Check generator share links
    if(typeof knife_generator_options.share_links === 'undefined') {
      return false;
    }

    // Check generator permalink
    if(typeof knife_generator_options.permalink === 'undefined') {
      return false;
    }

    var buttons = generator.querySelectorAll('.share > .share__link');

    var matches = [
      knife_generator_options.permalink.replace(/\/?$/, '/') + index + '/', caption
    ];

    for(var i = 0, link; button = buttons[i]; i++) {
      var label = button.getAttribute('data-label');

      if(typeof knife_generator_options.share_links[label] === 'undefined') {
        continue;
      }

      var options = knife_generator_options.share_links[label];

      button.href = options.link.replace(/%([\d])\$s/g, function(match, i) {
        return encodeURIComponent(matches[i - 1]);
      });
    }

    if(window.shareButtons === 'function') {
      window.shareButtons();
    }
  }


  /**
   * Create loader
   */
  (function() {
    var loader = document.createElement('div');
    loader.classList.add('entry-generator__loader');
    generator.insertBefore(loader, generator.firstChild);

    var bounce = document.createElement('span');
    bounce.classList.add('entry-generator__loader-bounce');
    loader.appendChild(bounce);
  })();


  /**
   * Set color options
   */
  (function() {
    // Set background color
    if(typeof knife_generator_options.page_background !== 'undefined') {
      generator.style.backgroundColor = knife_generator_options.page_background;
    }

    // Set text color
    if(typeof knife_generator_options.page_color !== 'undefined') {
      generator.style.color = knife_generator_options.page_color;
    }
  })();


  /**
   * Create generator button
   */
  (function() {
    if(typeof knife_generator_options.button_text === 'undefined') {
      return false;
    }

    // Create button outer element
    var wrapper = document.createElement('div');
    wrapper.classList.add('entry-generator__button');
    generator.appendChild(wrapper);

    // Create start button
    start = document.createElement('button');
    start.classList.add('button');
    start.setAttribute('type', 'button');
    start.textContent = knife_generator_options.button_text;
    wrapper.appendChild(start);

    // Set button color
    if(typeof knife_generator_options.button_background !== 'undefined') {
      start.style.backgroundColor = knife_generator_options.button_background;
    }

    // Set button text color
    if(typeof knife_generator_options.button_color !== 'undefined') {
      start.style.color = knife_generator_options.button_color;
    }
  })();


  /**
   * Generate button click
   */
  start.addEventListener('click', function(e) {
    e.preventDefault();

    var rand = Math.floor(Math.random() * items.length);
    var item = items[rand];

    replaceShare(item.caption, rand);
    return;

//    return generator.share([1, 2]);


    var poster = new Image();
    poster.setAttribute('src', item.poster);


    poster.addEventListener('load', function() {
      poster.classList.add('entry-generator__poster');
//      loader.parentNode.removeChild(loader);
//      generator.insertBefore(poster, generator.firstChild);
    });


    generator.classList.add('entry-generator--loader');
    return;

    var set = Math.floor(Math.random() * items.length);
    var item = items[set];

    if(repeat === false) {
      generator.querySelector('.entry-generator__share').style.display = 'flex';

      var title = generator.querySelector('.entry-generator__title');
      title.parentNode.removeChild(title);

      if(typeof knife_generator_options.button_repeat !== 'undefined') {
        start.textContent = knife_generator_options.button_repeat;
      }

      repeat = true;
    }

    if(typeof item.description !== 'undefined') {
      generator.querySelector('.entry-generator__content').innerHTML = item.description;
    }

    if(typeof item.poster !== 'undefined') {
      var poster = generator.querySelector('.entry-generator__poster');

      if(poster === null) {
        poster = new Image();
        poster.classList.add('entry-generator__poster');
        generator.insertBefore(poster, generator.firstChild);
      }

      poster.setAttribute('src', item.poster);

      if(typeof item.caption !== 'undefined') {
        poster.setAttribute('alt', item.caption);
      }
    }
  });
})();

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
   * Create bounce loader
   */
  function createLoader(embed) {
    var loader = document.createElement('div');
    loader.classList.add('entry-generator__loader');
    embed.appendChild(loader);

    var bounce = document.createElement('span');
    bounce.classList.add('entry-generator__loader-bounce');
    loader.appendChild(bounce);

    return loader;
  }


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
   *
   * TODO: remake share and add animations
   */
  start.addEventListener('click', function(e) {
    e.preventDefault();

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

    set = set + 1;
    var link = knife_generator_options.url + set + '/';

    var networks = {
      vkontakte: 'https://vk.com/share.php?url=' + encodeURIComponent(link)  + '&text=' + encodeURIComponent(item.caption),
      facebook: 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(link),
      telegram: 'https://t.me/share/url?url=' + encodeURIComponent(link) + '&text=' + encodeURIComponent(item.caption),
      twitter: 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(item.caption) + '&url=' + encodeURIComponent(link)
    }

    generator.querySelector('.entry-generator__share .share__link--vkontakte').setAttribute('href', networks.vkontakte);
    generator.querySelector('.entry-generator__share .share__link--facebook').setAttribute('href', networks.facebook);
    generator.querySelector('.entry-generator__share .share__link--telegram').setAttribute('href', networks.telegram);
    generator.querySelector('.entry-generator__share .share__link--twitter').setAttribute('href', networks.twitter);
  });
})();

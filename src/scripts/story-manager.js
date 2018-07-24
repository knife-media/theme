/**
 * Create story using Glide slider
 *
 * @since 1.3
 */

(function() {
  var story = document.querySelector('.glide');


  /**
   * Check if Glide object exists
   */
  if(story === null || typeof Glide === 'undefined') {
    return false;
  }


  /**
   * Check slides count before story creation
   */
  if(story.querySelectorAll('.glide__slide').length === 0) {
    return false;
  }


  /**
   * Reload page if user go back with browser cache
   *
   * @link https://stackoverflow.com/a/13123626
   */
  window.addEventListener('pageshow', function(event) {
    if(event.persisted) {
      window.location.reload(false)
    }
  });


  /**
   * Set story height on and slow slider load
   */
  window.addEventListener('load', function() {
    var offset = story.getBoundingClientRect();
    story.style.height = window.innerHeight - offset.top - window.pageYOffset + 'px';

    return story.classList.add('glide--active');
  });


  /*
   * Create Glide instance with custom options
   */
  var glide = new Glide('.glide', {
    gap: 0, rewind: false, dragThreshold: false, touchAngle: 30
  });


  /**
   * Add preloader to story
   */
  glide.on('mount.before', function() {
    var loader = document.createElement('div');
    loader.classList.add('glide__loader', 'icon', 'icon--loop');

    return story.appendChild(loader);
  });


  /**
   * Set custom background
   */
  glide.on('mount.before', function() {
    if(typeof knife_story_options.background === 'undefined') {
      return false;
    }

    var media = document.createElement('div');
    media.classList.add('glide__backdrop');
    media.style.backgroundImage = 'url(' + knife_story_options.background + ')';

    // Append blur element
    (function() {
      if(typeof knife_story_options.blur === 'undefined') {
        return false;
      }

      var blur = parseInt(knife_story_options.blur);

      if(blur <= 0) {
        return false;
      }

      // Add negative margins to blured backdrop
      // https://stackoverflow.com/a/12224347/
      media.style.margin = '-' + blur + 'px';

      // Add blur
      media.style.filter = 'blur(' + blur + 'px)';
    })();


    // Append shadow element
    (function() {
      if(typeof knife_story_options.shadow === 'undefined') {
        return false;
      }

      var alpha = parseInt(knife_story_options.shadow) / 100;

      if(alpha <= 0 || alpha > 1) {
        return false;
      }

      var shadow = document.createElement('div');
      shadow.classList.add('glide__shadow');
      shadow.style.backgroundColor = 'rgba(0, 0, 0, ' + alpha + ')';

      media.appendChild(shadow);
    })();

    return story.appendChild(media);
  });


  /**
   * Add bullets on Glide mounting
   */
  glide.on('mount.before', function() {
    var bullets = document.createElement('div');
    bullets.classList.add('glide__bullets');

    for (var i = 0; i < story.querySelectorAll('.glide__slide').length; i++) {
      var item = document.createElement('span');
      item.classList.add('glide__bullets-item');

      bullets.appendChild(item);
    }

    return story.appendChild(bullets);
  });


  /**
   * Add bullets events
   */
  glide.on(['mount.after', 'run'], function() {
    var bullets = story.querySelectorAll('.glide__bullets-item');

    for (var i = 0, bullet; bullet = bullets[i]; i++) {
      bullet.classList.remove('glide__bullets-item--active');

      if(glide.index === i) {
        bullet.classList.add('glide__bullets-item--active');
      }
    }
  });


  /**
   * Create empty slide if next post availible
   */
  glide.on('mount.before', function(move) {
    var link = document.querySelector('link[rel="next"]');

    if(link === null || !link.hasAttribute('href')) {
      return false;
    }

    var empty = document.createElement('div');
    empty.classList.add('glide__slide', 'glide__slide--empty');

    glide.update({
      href: link.href
    });

    return story.querySelector('.glide__slides').appendChild(empty);
  });


  /**
   * Add slider controls on Glide mounting
   */
  glide.on('mount.before', function() {
    ['prev', 'next'].forEach(function(cl, i) {
      var control = document.createElement('div');
      control.classList.add('glide__control', 'glide__control--' + cl);
      control.classList.add();

      var icon = document.createElement('span');
      icon.classList.add('icon', 'icon--' + cl);
      control.appendChild(icon);

      story.appendChild(control);
    });

    story.querySelector('.glide__control--next').addEventListener('click', function(e) {
      e.preventDefault();

      glide.go('>');
    });

    story.querySelector('.glide__control--prev').addEventListener('click', function(e) {
      e.preventDefault();

      glide.go('<');
    });
  });


  /**
   * Add last slide index to settings
   */
  glide.on('mount.after', function() {
    var slides = story.querySelectorAll('.glide__slide');

    glide.update({
      count: slides.length - 1
    });
  });


  /**
   * Manage prev slider control
   */
  glide.on(['mount.after', 'run'], function(move) {
    var prev = story.querySelector('.glide__control--prev');

    prev.classList.remove('glide__control--disabled');

    if(glide.index === 0) {
      prev.classList.add('glide__control--disabled');
    }
  });


  /**
   * Manage next slider control
   */
  glide.on('run', function(move) {
    var next = story.querySelector('.glide__control--next');

    next.classList.remove('glide__control--disabled');

    if(glide.index === glide.settings.count) {
      next.classList.add('glide__control--disabled');
    }
  });


  /**
   * Hide story if extra slide exists
   */
  glide.on('run', function(move) {
    if(glide.index === glide.settings.count && glide.settings.href) {
      story.classList.remove('glide--active');
    }
  });


  /**
   * Load next story if exists
   */
  glide.on('run.after', function(move) {
    if(glide.index === glide.settings.count && glide.settings.href) {
      document.location.href = glide.settings.href;
    }
  });


  /**
   * Let's rock!
   */
  return glide.mount();
})();

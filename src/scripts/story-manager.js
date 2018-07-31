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
   * Check slides existsing before story creation
   *
   * @link https://github.com/glidejs/glide/issues/224
   */
  if(story.querySelectorAll('.glide__slide').length === 0) {
    return false;
  }


  /*
   * Create Glide instance with custom options
   */
  var glide = new Glide('.glide', {
    gap: 0, rewind: false, touchAngle: 45,
    dragThreshold: false,
    breakpoints: {
      767: {
        dragThreshold: true
      }
    }
  });


  /**
   * Declare global options object
   */
  var options = {}


  /**
   * Add slides
   */
  glide.on('mount.before', function() {
    if(typeof knife_story_stories === 'undefined' || knife_story_stories.length < 1) {
      return false;
    }

    for (var i = 0, item; item = knife_story_stories[i]; i++) {
      var slide = document.createElement('div');
      slide.classList.add('glide__slide-content');

      // Append kicker
      (function(){
        if(typeof item.kicker === 'undefined') {
          return false;
        }

        var kicker = document.createElement('div');
        kicker.classList.add('glide__slide-kicker');
        kicker.innerHTML = item.kicker;

        slide.appendChild(kicker);
      })();


      // Append media
      (function(){
        if(typeof item.image === 'undefined' || typeof item.ratio === 'undefined') {
          return false;
        }

        var image = document.createElement('div');
        image.classList.add('glide__slide-image');
        image.style.setProperty('background-image', 'url(' + item.image + ')');
        image.style.setProperty('--image-ratio', item.ratio);

        slide.appendChild(image);
      })();


      // Append entry
      (function(){
        if(typeof item.entry === 'undefined') {
          return false;
        }

        var entry = document.createElement('div');
        entry.classList.add('glide__slide-entry');
        entry.innerHTML = item.entry;

        slide.appendChild(entry);
      })();


      var wrap = document.createElement('div');
      wrap.classList.add('glide__slide-wrap');
      wrap.appendChild(slide);

      var block = document.createElement('div');
      block.classList.add('glide__slide');
      block.appendChild(wrap);

      story.querySelector('.glide__slides').appendChild(block);
    }
  });


  /**
   * Apend share buttons to the last slide
   */
  glide.on('mount.before', function() {
    var slides = story.querySelectorAll('.glide__slide');

    if(slides.length <= 1) {
      return false;
    }

    var share = slides[0].querySelector('.share');

    if(share === null) {
      return false
    }

    var clone = share.cloneNode(true);
    var slide = slides[slides.length - 1];

    if(typeof knife_story_options.action !== 'undefined') {
      var links = clone.querySelectorAll('.share__link');

      for (var i = 0, link; link = links[i]; i++) {
        link.setAttribute('data-action', knife_story_options.action);
      }
    }

    slide.querySelector('.glide__slide-content').appendChild(clone);

    return window.shareButtons();
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
   * Create empty slide if next post availible
   */
  glide.on('mount.before', function(move) {
    var link = document.querySelector('link[rel="prev"]');

    if(link === null || !link.hasAttribute('href')) {
      return false;
    }

    options.href = link.href;

    var empty = document.createElement('div');
    empty.classList.add('glide__slide', 'glide__slide--empty');

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
   * Set custom background
   */
  glide.on('mount.after', function() {
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
   * Add last slide index to options
   */
  glide.on('mount.after', function() {
    var slides = story.querySelectorAll('.glide__slide');
    options.count = slides.length - 1
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

    if(glide.index === options.count) {
      next.classList.add('glide__control--disabled');
    }
  });


  /**
   * Hide story if extra slide exists
   */
  glide.on('run', function(move) {
    if(glide.index === options.count && options.href) {
      story.classList.remove('glide--active');
    }
  });


  /**
   * Load next story if exists
   */
  glide.on('run.after', function(move) {
    if(glide.index === options.count && options.href) {
      document.location.href = options.href;
    }
  });


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


  /**
   * Disable touch bounce effect
   */
  glide.on('build.after', function() {
    var start = 0;

    story.addEventListener('touchstart', function(e) {
      var touch = e.changedTouches[0];

      start = touch.pageY;
    }, true);


    story.addEventListener('touchmove', function(e) {
      var touch = e.changedTouches[0];

      if(start < touch.pageY && window.pageYOffset === 0) {
        e.preventDefault();
      }
    }, true);
  });


  /**
   * Let's rock!
   */
  return glide.mount();
})();

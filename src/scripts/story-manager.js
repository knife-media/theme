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
   * Define global vars
   */
  var count = story.querySelectorAll('.glide__slide').length;


  /**
   * Check slides count before story creation
   */
  if(count === 0) {
    return false;
  }


  /*
   * Create Glide instance with custom options
   */
  var glide = new Glide('.glide', {
    gap: 0, rewind: false, dragThreshold: false
  });


  /**
   * Set custom background
   */
  glide.on('mount.before', function() {
    if(typeof knife_story_options.background === 'undefined') {
      return false;
    }

    var image = document.createElement('div');
    image.classList.add('glide__backdrop');
    image.style.backgroundImage = 'url(' + knife_story_options.background + ')';

    if(typeof knife_story_options.blur !== 'undefined' && parseInt(knife_story_options.blur) > 0) {
      image.style.filter = 'blur(' + knife_story_options.blur + 'px)';
    }

    story.appendChild(image);

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

    image.appendChild(shadow);
  });



  /**
   * Add bullets on Glide mounting
   */
  glide.on('mount.before', function() {
    var bullets = document.createElement('div');
    bullets.classList.add('glide__bullets');

    for (var i = 0; i < count; i++) {
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
  glide.on(['mount.after', 'run'], function(move) {
    var next = story.querySelector('.glide__control--next');

    next.classList.remove('glide__control--disabled');

    if(glide.index + 1 === count) {
      next.classList.add('glide__control--disabled');
    }
  });


  /**
   * Let's rock!
   */
  return glide.mount();

})();

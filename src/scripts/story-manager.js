/**
 * Create story using Glide slider
 *
 * @since 1.3
 */

(function() {
  var glide = document.querySelector('.glide');


  /**
   * Check if Glide object exists
   */
  if(glide === null || typeof Glide === 'undefined') {
    return false;
  }


  /**
   * Define global vars
   */
  var count = glide.querySelectorAll('.glide__slide').length;


  /**
   * Check slides count before story creation
   */
  if(count === 0) {
    return false;
  }


  /*
   * Create Glide instance with custom options
   */
  var story = new Glide('.glide', {
    gap: 0, rewind: false, dragThreshold: false
  });


  /**
   * Set custom background
   */
  story.on('mount.before', function() {
    if(typeof knife_story_options.background === 'undefined') {
      return false;
    }

    var image = document.createElement('div');
    image.classList.add('glide__backdrop');
    image.style.backgroundImage = 'url(' + knife_story_options.background + ')';

    if(typeof knife_story_options.blur !== 'undefined' && parseInt(knife_story_options.blur) > 0) {
      image.style.filter = 'blur(' + knife_story_options.blur + 'px)';
    }

    glide.appendChild(image);

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
  story.on('mount.before', function() {
    var bullets = document.createElement('div');
    bullets.classList.add('glide__bullets');

    for (var i = 0; i < count; i++) {
      var item = document.createElement('span');
      item.classList.add('glide__bullets-item');

      bullets.appendChild(item);
    }

    return glide.appendChild(bullets);
  });


  /**
   * Add bullets events
   */
  story.on(['mount.after', 'run'], function() {
    var bullets = glide.querySelectorAll('.glide__bullets-item');

    for (var i = 0, bullet; bullet = bullets[i]; i++) {
      bullet.classList.remove('glide__bullets-item--active');

      if(story.index === i) {
        bullet.classList.add('glide__bullets-item--active');
      }
    }
  });


  /**
   * Add slider controls on Glide mounting
   */
  story.on('mount.before', function() {
    ['prev', 'next'].forEach(function(cl, i) {
      var control = document.createElement('div');
      control.classList.add('glide__control', 'glide__control--' + cl);
      control.classList.add();

      var icon = document.createElement('span');
      icon.classList.add('icon', 'icon--' + cl);
      control.appendChild(icon);

      glide.appendChild(control);
    });

    glide.querySelector('.glide__control--next').addEventListener('click', function(e) {
      e.preventDefault();

      story.go('>');
    });

    glide.querySelector('.glide__control--prev').addEventListener('click', function(e) {
      e.preventDefault();

      story.go('<');
    });
  });


  /**
   * Let's rock!
   */
  return story.mount();

})();

(function() {
  var slider = document.querySelector('.slider');

  // Check if Swiper object exists
  if(typeof Swiper === 'undefined')
    return false;


  // Check slider element and options meta
  if(slider === null || typeof knife_story_options === 'undefined')
    return false;


  // Set background and shadow using stories options
  var setBackground = function(element) {
    if(typeof knife_story_options.background === 'undefined')
      return false;

    var image = document.createElement('div');
    image.classList.add('slider__background');
    image.style.backgroundImage = 'url(' + knife_story_options.background + ')';

    element.appendChild(image);

    if(typeof knife_story_options.shadow === 'undefined')
      return image;

    var alpha = parseInt(knife_story_options.shadow) / 100;

    if(alpha <= 0 || alpha > 1)
      return image;

    var shadow = document.createElement('div');
    shadow.classList.add('slider__shadow');
    shadow.style.backgroundColor = 'rgba(0, 0, 0, ' + alpha + ')';

    image.appendChild(shadow);

    return image;
  }


  // Create navigation
  var createNavigation = function() {

    // Create next button
    var next = document.createElement('div');
    next.classList.add('swiper-button-next');

    slider.appendChild(next);

    // Create prev button
    var prev = document.createElement('div');
    prev.classList.add('swiper-button-prev');

    slider.appendChild(prev);

    return {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev'
    }
  }


  // Create pagination
  var createPagination = function() {

    // Create pagination element
    var bullets = document.createElement('div');
    bullets.classList.add('swiper-pagination');

    slider.appendChild(bullets);

    return {
      el: '.swiper-pagination',
      type: 'bullets'
    }
  }


  // Insert stories to slider
  var insertStories = function() {
    if(typeof knife_story_stories === 'undefined' || knife_story_stories.length < 1)
      return false;

    for(var i = 0; i < knife_story_stories.length; i++) {
      var slide = document.createElement('div');
      slide.classList.add('swiper-slide');

      var item = document.createElement('div');
      item.classList.add('slider__item', 'block');
      slide.appendChild(item);

      var content = document.createElement('div');
      content.classList.add('slider__item-content', 'custom');
      content.innerHTML = knife_story_stories[i];
      item.appendChild(content);

      swiper.appendSlide(slide);
    }
  }


  // Apply effects using options
  var applyEffects = function(args) {

    // Set background image
    var background = setBackground(slider);

    if(typeof knife_story_options.effect === 'undefined' || !background)
      return args;

    switch(knife_story_options.effect) {
      case 'parallax':

        background.style.width = 'calc(100% + 200px)';
        background.setAttribute('data-swiper-parallax-x', '-200');
        background.setAttribute('data-swiper-parallax-duration', 700);

        break;
    }

    return args;
  }


  // Set swiper options
  var setOptions = function() {
    var args = {};

    // Allow keyboard shortcuts
    args.keyboard = true;

    // Slider speed
    args.speed = 400;

    // Get paginations elements
    args.pagination = createPagination();

    // Get navigation element
    args.navigation = createNavigation();

    // add simulate touch only on small screen
    args.simulateTouch = (window.innerWidth < 768);

    return applyEffects(args);
  }


  // Create slider
  var swiper = new Swiper('.slider', setOptions());

  return insertStories();
})();

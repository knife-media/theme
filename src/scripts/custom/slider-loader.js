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

    if(typeof knife_story_options.blur !== 'undefined' && parseInt(knife_story_options.blur) > 0)
      image.style.filter = 'blur(' + knife_story_options.blur + 'px)';

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


  // Create entry tag with html on slide insertion
  var createEntry = function(story, item) {
    if(typeof story.entry === 'undefined')
      return;

    var entry = document.createElement('div');
    entry.classList.add('slider__item-entry', 'custom');
    entry.innerHTML = story.entry;
    item.appendChild(entry);
  }


  // Create image figure with optional caption on slide insertion
  var createImage = function(story, item) {
    if(typeof story.image === 'undefined')
      return;

    var figure = document.createElement('figure');
    figure.classList.add('slider__item-media');
    item.appendChild(figure);

    var image = document.createElement('img');
    image.classList.add('slider__item-image');
    image.src = story.image;
    figure.appendChild(image);

    if(typeof story.caption === 'undefined')
      return;

    var caption = document.createElement('figcaption');
    caption.classList.add('slider__item-caption');
    caption.innerHTML = story.caption;
    figure.appendChild(caption);
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

      // Set entry if exists
      createEntry(knife_story_stories[i], item);

      // Set image if exists
      createImage(knife_story_stories[i], item);

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

        background.style.width = 'calc(100% + 210px)';
        background.setAttribute('data-swiper-parallax-x', -200);
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

    // Disable resistance
    args.resistanceRatio = 999;

    // Add simulate touch only on small screen
    args.simulateTouch = (window.innerWidth < 768);

    return applyEffects(args);
  }


  // Create slider
  var swiper = new Swiper('.slider', setOptions());

  return insertStories();
})();

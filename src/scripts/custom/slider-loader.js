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
      return false;

    var alpha = parseInt(knife_story_options.shadow) / 100;

    if(alpha <= 0 || alpha > 1)
      return false;

    var shadow = document.createElement('div');
    shadow.classList.add('slider__shadow');
    shadow.style.backgroundColor = 'rgba(0, 0, 0, ' + alpha + ')';

    image.appendChild(shadow);
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


  // Create slider
  var createSlider = function() {
    var args = {
      keyboard: true,
      simulateTouch: false,
      speed: 400,
      parallax: true,
      updateOnImagesReady: true,
      pagination: createPagination(),
      navigation: createNavigation()
    }
    console.log(args);

    return new Swiper('.slider', args);
  }

  var swiper = createSlider();

  swiper.on('touchStart', function() {
    document.querySelector('.swiper-button-next').classList.add('swiper-button-hidden');
    document.querySelector('.swiper-button-prev').classList.add('swiper-button-hidden');
  });

  swiper.on('touchEnd', function() {
    document.querySelector('.swiper-button-next').classList.remove('swiper-button-hidden');
    document.querySelector('.swiper-button-prev').classList.remove('swiper-button-hidden');
  });

    slider.style.opacity = 1;




  setBackground(slider);


  for(var i = 0; i < knife_story_stories.length; i++) {

    var slide = document.createElement('div');
    slide.classList.add('swiper-slide');
    slide.innerHTML = '<div class="slider__item block"><div class="slider__item-content custom">' + knife_story_stories[i] + '</div></div>';

    swiper.appendSlide(slide);
//    setBackground(slide);
  }

})();

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

    image.setAttribute('data-swiper-parallax', '-5%');
    image.setAttribute('data-swiper-parallax-duration', '1000');
    image.style.width = '110%';

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



    slider.style.opacity = 1;

  setBackground(slider);

	var swiper = new Swiper('.swiper-container', {
    effect: 'slide',
    speed: 600,
		parallax: true,
			hashNavigation: {
			  watchState: true,
			},
		pagination: createPagination(),
    navigation:createNavigation(),
    keyboard: true,
        on: {
            init: function () {
                console.log('swiper initialized');
            },
          touchStart: function() {
            document.querySelector('.swiper-button-next').style.opacity = 0;
          },
          touchEnd: function() {
          document.querySelector('.swiper-button-next').style.opacity = 1;

          }

        }
	});

  for(var i = 0; i < knife_story_stories.length; i++) {
    console.log(1);

    var slide = document.createElement('div');
    slide.classList.add('swiper-slide');
    slide.innerHTML = '<div class="slider__item block">' + knife_story_stories[i] + '</div>';
    slide.setAttribute('data-hash', 'slider-' + i);

    swiper.appendSlide(slide);
//    setBackground(slide);
  }

})();

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

    image.setAttribute('data-swiper-parallax', '-20%');
    image.style.width = '140%';

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

var pag = document.createElement('div');
    pag.classList.add('swiper-pagination');

    slider.appendChild(pag);



    slider.style.opacity = 1;

  setBackground(slider);

	var swiper = new Swiper('.swiper-container', {
    effect: 'slide',
		parallax: true,
			hashNavigation: {
			  watchState: true,
			},
		pagination: {
			el: '.swiper-pagination',
			type: 'bullets',
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
        on: {
            init: function () {
                console.log('swiper initialized');
            },
        }
	});

  for(var i = 0; i < knife_story_stories.length; i++) {
    console.log(1);

    var slide = document.createElement('div');
    slide.classList.add('swiper-slide');
    slide.innerHTML = '<div class="slider__item block">' + knife_story_stories[i] + '</div>';

    swiper.appendSlide(slide);
//    setBackground(slide);
  }

})();

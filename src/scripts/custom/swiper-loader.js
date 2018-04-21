(function() {
	var slider = document.querySelector('.slider');

	// Check if Swiper object exists
	if(typeof Swiper === 'undefined')
		return false;


    // Check slider element and options meta
    if(slider === null || typeof knife_story_meta === 'undifined')
        return false;

    console.log(knife_story_meta);

        var p = document.createElement("div");
        p.classList.add('slider__background');
        p.style.backgroundImage = 'url(' + knife_story_meta.background + ')';

         slider.appendChild(p);



	slider.style.opacity = 1;

	return new Swiper('.swiper-container', {
		parallax: true,
			hashNavigation: {
			  watchState: true,
			},
		pagination: {
			el: '.swiper-pagination',
			type: 'progressbar',
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		}
	});

})();

(function() {
	var slider = document.querySelector('.slider');

	// Check if slider element and Swiper object exists
	if(slider === null || typeof Swiper === 'undefined')
		return false;


	slider.style.opacity = 1;

	return new Swiper('.swiper-container', {
		parallax: true,
			hashNavigation: {
			  watchState: true,
			},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		}
	});

})();

(function() {
    var menu = document.getElementById('toggle-menu');
	var search = document.getElementById('toggle-search');

	// Toggle menu bar
	menu.addEventListener('click', function(e) {
		e.preventDefault();

		if(document.querySelector('.search').classList.contains('search--expand'))
			search.click();

		document.querySelector('.topline__menu').classList.toggle('topline__menu--expand');

		return this.classList.toggle('toggle--expand');
	});


	// Toggle search bar
	search.addEventListener('click', function(e) {
		e.preventDefault();

 		if(document.querySelector('.topline__menu').classList.contains('topline__menu--expand'))
			menu.click();

		document.querySelector('.search').classList.toggle('search--expand');
		document.body.classList.toggle('body--search');

		return this.classList.toggle('toggle--expand');
	});


	// Close menu and search on ESC
	window.addEventListener('keydown', function(e) {
		e = e || window.event;

		if(e.keyCode !== 27)
			return false;

		if(document.querySelector('.search').classList.contains('search--expand'))
			search.click();

 		if(document.querySelector('.topline__menu').classList.contains('topline__menu--expand'))
			menu.click();
	}, true);
})();

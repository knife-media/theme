(function() {
    var parent = '.search';

    if(document.querySelector(parent) === null)
		return false;

	document.querySelector('.topline .toggle--search').addEventListener('click', function(e) {
		e.preventDefault();

		document.querySelector(parent).classList.toggle('search--expand');
		document.body.classList.toggle('body--search');

		return this.classList.toggle('toggle--expand');
	});

	window.addEventListener('keydown', function(e) {
		e = e || window.event;

		if(e.keyCode !== 27)
			return false;

		if(!document.querySelector(parent).classList.contains('search--expand'))
			return false;

		return document.querySelector('.topline .toggle--search').click();
	}, true);
})();

(function() {
    var parent = '.search';

    if(document.querySelector(parent).length < 1)
		return false;

	document.querySelector('.topline .toggle--search').addEventListener('click', function(e) {
		e.preventDefault();

		document.querySelector(parent).classList.toggle('search--expand');
		document.body.classList.toggle('body--search');

		return this.classList.toggle('toggle--expand');
	});
})();

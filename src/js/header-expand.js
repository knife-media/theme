(function() {
	var parent = '.topline__menu';

	if(document.querySelector(parent) === null)
		return false;

	document.querySelector('.topline .toggle--menu').addEventListener('click', function(e) {
		e.preventDefault();

		document.querySelector(parent).classList.toggle('topline__menu--expand');

		return this.classList.toggle('toggle--expand');
	});
})();

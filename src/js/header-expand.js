(function(){
	document.querySelector('.topline .toggle--menu').addEventListener('click', function(e) {
		e.preventDefault();

		document.querySelector('.topline__menu').classList.toggle('topline__menu--expand');

		return this.classList.toggle('toggle--expand');
	});
})();

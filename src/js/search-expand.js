(function(){
	document.querySelector('.topline .toggle--search').addEventListener('click', function(e) {
		e.preventDefault();

		document.querySelector('.search').classList.toggle('search--expand');
		document.body.classList.toggle('body--search');

		return this.classList.toggle('toggle--expand');
	});
})();

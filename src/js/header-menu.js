(function(){
	var topline = document.querySelector('.topline');

	topline.querySelector('.topline__button--menu').addEventListener('click', function(e) {
    e.preventDefault();

    topline.querySelector('.topline__menu').classList.toggle('topline__menu--expand');
		return this.classList.toggle('toggle--expand');
  })
})();

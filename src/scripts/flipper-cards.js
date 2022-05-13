/**
 * Add flipper cards dynamic
 *
 * @since 1.12
 */

(function () {
  var post = document.querySelector('.post');

  if (post === null) {
    return false;
  }


  var cards = post.querySelectorAll('.figure--flipper');

  for (var i = 0; i < cards.length; i++) {
    cards[i].addEventListener('click', function (e) {
      var target = e.target || e.srcElement;

      // Check if element is link
      if (target.tagName !== 'A') {
        this.classList.toggle('figure--rotate');
      }
    });
  }
})();
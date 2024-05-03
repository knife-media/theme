/**
 * Scroller button handler
 *
 * @since 1.17
 */

(function () {
  const scroller = document.querySelector('.scroller');

  if ( scroller === null ) {
    return;
  }

  let isVisible = false;

  function handleScroll() {
    const scrollY = window.scrollY || window.pageYOffset;

    if (scrollY > 100 && !isVisible) {
      scroller.classList.add('scroller--visible');
      isVisible = true;
    } else if (scrollY <= 100 && isVisible) {
      scroller.classList.remove('scroller--visible');
      isVisible = false;
    }
  }

  document.addEventListener('scroll', handleScroll, { passive: true });

  scroller.addEventListener('click', function(e) {
    e.preventDefault();

    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();

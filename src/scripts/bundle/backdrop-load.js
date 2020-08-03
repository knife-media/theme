/**
 * Set page background image and color
 *
 * @version 1.8
 */

(function () {
  /**
   * Check if backdrop options exist
   */
  if (typeof knife_backdrop === 'undefined') {
    return false;
  }


  /**
   * Set body background color only if image not set
   */
  if (typeof knife_backdrop.image === 'undefined') {

    // Set body background color
    if (typeof knife_backdrop.color !== 'undefined') {
      document.body.style.backgroundColor = '#' + knife_backdrop.color;
    }

    return false;
  }


  /**
   * Create backdrop element
   */
  var backdrop = document.createElement('div');
  backdrop.classList.add('backdrop');


  /**
   * Set background image url
   */
  backdrop.style.backgroundImage = 'url(' + knife_backdrop.image + ')';


  /**
   * Set background size if exists
   */
  if (typeof knife_backdrop.size !== 'undefined') {
    backdrop.style.backgroundSize = knife_backdrop.size;
  }

  var image = new Image();
  image.addEventListener('load', function () {
    document.body.insertBefore(backdrop, document.body.firstChild);
  });

  return image.src = knife_backdrop.image;
})();
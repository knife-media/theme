(function() {
  /**
   * Check if backdrop options exist
   */
  if(typeof knife_backdrop === 'undefined') {
    return false;
  }


  /**
   * Create element
   */
  var backdrop = document.createElement('div');
  backdrop.classList.add('backdrop');


  /**
   * Set body background color
   */
  if(typeof knife_backdrop.color !== 'undefined') {
    document.body.style.backgroundColor = '#' + knife_backdrop.color;
  }


  /**
   * Apply element if image is not set
   */
  if(typeof knife_backdrop.image === 'undefined') {
    return document.body.appendChild(backdrop);
  }

  backdrop.style.backgroundImage = 'url(' + knife_backdrop.image + ')';


  /**
   * Set background size if exists
   */
  if(typeof knife_backdrop.size !== 'undefined') {
    backdrop.style.backgroundSize = knife_backdrop.size;
  }

  var image = new Image();
  image.addEventListener('load', function() {
    document.body.appendChild(backdrop);
  });

  return image.src = knife_backdrop.image;
})();

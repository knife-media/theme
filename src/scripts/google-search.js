(function() {
  /**
   * Check if search id and template defined
   */
  if(typeof knife_search_id === 'undefined') {
    return false;
  }


  /**
   * Init search on separate page
   */
  if(document.querySelector('.search')) {
    // Load gsce to page
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = 'https://cse.google.com/cse.js?cx=' + knife_search_id;

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  }
})();

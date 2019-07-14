jQuery(document).ready(function($) {
  var box = $("#knife-promo-box");

  /**
   * Define checkbox
   */
  var check = box.find('input[type="checkbox"]');


  /**
   * Toggle options on promo checkbox
   */
  check.on('click', function() {
    box.find('.promo').toggleClass('hidden',
        $(this).is(':not(:checked)')
    );
  });


  /**
   * Define text color input as colorpicker
   */
  box.find('.color-picker').wpColorPicker();


  /**
   * Remove hidden class for promo posts on load
   */
  if(check.is(':checked')) {
    box.find('.promo').removeClass('hidden');
  }
});

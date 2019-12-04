jQuery(document).ready(function($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $("#knife-promo-box");

  /**
   * Check required metabox options
   */
  if(typeof knife_promo_metabox === 'undefined') {
    return false;
  }


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
   * Process select image button click
   */
  box.on('click', 'a.upload', function(e) {
    e.preventDefault();

    // Create custom state
    var state = wp.media.controller.Library.extend({
      defaults: _.defaults({
        filterable: 'all',
      }, wp.media.controller.Library.prototype.defaults)
    });

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_promo_metabox.choose || '',
      multiple: false,
      states: [
        new state()
      ]
    });

    // On image select
    frame.on('select', function() {
      var attachment = frame.state().get('selection').first().toJSON();

      box.find('input.logo').val(attachment.url);
    });

    frame.open();
  });


  /**
   * Remove hidden class for promo posts on load
   */
  if(check.is(':checked')) {
    box.find('.promo').removeClass('hidden');
  }
});

jQuery(document).ready(function($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-background-box');


  /**
   * Check required metabox options
   */
  if(typeof knife_background_metabox === 'undefined') {
    return false;
  }


  /**
   * Define text color input as colorpicker
   */
  box.find('.color-picker').wpColorPicker();


  /**
   * Toggle background
   */
  function toggleBackground() {
    var input = box.find('input.image').val();

    box.find('select.size').attr('disabled', 'disabled');
    box.find('button.remove').attr('disabled', 'disabled');

    // Remove image if exists
    box.find('img').remove();

    if(input && input.length > 1) {
      $('<img />', {src: input}).prependTo(box).css('max-width', '100%');

      box.find('button.remove').removeAttr('disabled');
      box.find('select.size').removeAttr('disabled');
    }
  }


  /**
   * Process select image button click
   */
  box.on('click', 'button.select', function(e) {
    e.preventDefault();

    // Create custom state
    var state = wp.media.controller.Library.extend({
      defaults: _.defaults({
        filterable: 'all',
      }, wp.media.controller.Library.prototype.defaults)
    });

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_background_metabox.choose || '',
      multiple: false,
      states: [
        new state()
      ]
    });

    // On image select
    frame.on('select', function() {
      var attachment = frame.state().get('selection').first().toJSON();

      box.find('input.image').val(attachment.url);

      return toggleBackground();
    });

    frame.open();
  });


  /**
   * Delete image on link clicking
   */
  box.on('click', 'button.remove', function(e) {
    e.preventDefault();

    box.find('input.image').val('');

    return toggleBackground();
  });


  /**
   * Launch toggle on load
   */
  return toggleBackground();
});

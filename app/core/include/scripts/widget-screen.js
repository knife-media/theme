(function($) {
  /**
   * Widget colorpicker
   */
  $(document).ready(function() {
    var initColorPicker = function(widget) {
      widget.find('.color-picker').wpColorPicker({
        change: _.throttle(function() {
          $(this).trigger('change');
        }, 3000),

        clear: _.throttle(function() {
            $(this).trigger('change');
        }, 4000)
      });
    }

    $('#widgets-right .widget:has(.color-picker)').each(function () {
      initColorPicker($(this));
    });

    $(document).on('widget-added widget-updated', function(event, widget) {
      initColorPicker(widget);
    });
  });


  /**
   * Widget cover image
   */
  $(document).on('click', '.knife-widget-image', function(e) {
    e.preventDefault();

    var widget = $(this).closest('.widget');
    var block = widget.find('.knife-widget-image').parent();

    var frame = wp.media({
      title: knife_widget_screen.choose,
      multiple: false
    });

    frame.on('open', function() {
      var selection = frame.state().get('selection');
      var selected = block.find('input[type="hidden"]').val();

      if(selected) {
        selection.add(wp.media.attachment(selected));
      }
    });

    frame.on('select', function() {
      var attachment = frame.state().get('selection').first().toJSON();

      block.find('input[type="hidden"]').val(attachment.id);
      block.find('input[type="hidden"]').trigger('change');

      block.find('img').remove();

      if(attachment.hasOwnProperty('url') && attachment.url.length > 0) {
        $('<img />', {src: attachment.url}).prependTo(block).css('max-width', '100%');
      }
    });

    return frame.open();
  });
}(jQuery));


(function($) {
  /**
   * Widget colorpicker
   */
  var initColorPicker = function(widget) {
    widget.find('.color-picker').wpColorPicker({
      change: function(e, ui) {
        $(e.target).val(ui.color.toString());
        $(e.target).trigger('change');
      },

      clear: function(e, ui) {
        $(e.target).trigger('change');
      }
    });
  }

  $(document).ready(function() {
    $('#wp_inactive_widgets .widget:has(.color-picker)').each(function() {
      initColorPicker($(this));
    });

    $('#widgets-right .widget:has(.color-picker)').each(function() {
      initColorPicker($(this));
    });

    $(document).on('widget-added widget-updated', function(event, widget) {
      initColorPicker(widget);
    });
  });


  /**
   * Widget adaptive checkbox
   */
  var initAdaptiveCheckbox = function(widget) {
    widget.find('.knife-widget-adaptive').change(function() {
      var wrapper = $(this).parent();

      wrapper.next().show();
      wrapper.nextAll().slice(1, 3).hide();

      if(this.checked) {
        wrapper.next().hide();
        wrapper.nextAll().slice(1, 3).show();
      }
    });

    widget.find('.knife-widget-adaptive').trigger('change');
  }

  $(document).ready(function() {
    $('#wp_inactive_widgets .widget:has(.knife-widget-adaptive)').each(function() {
      initAdaptiveCheckbox($(this));
    });

    $('#widgets-right .widget:has(.knife-widget-adaptive)').each(function() {
      initAdaptiveCheckbox($(this));
    });

    $(document).on('widget-added widget-updated', function(event, widget) {
      initAdaptiveCheckbox($(this));
    });
  });


  /**
   * Widget append button handler
   */
  var initWidgetButton = function(input) {
    if(input.val()) {
      return true;
    }

    var block = input.parent();
    var title = input.data('title') || '';

    // Create button
    var button = $('<button>', {'class': 'button', type: 'button'}).text(title);

    block.children().hide();

    button.on('click', function(e) {
      e.preventDefault();

      block.children().show();
      button.remove();
    });

    button.appendTo(block);
  }


  $(document).ready(function() {
    $('#widgets-right .widget:has(.knife-widget-button)').each(function() {
      var input = $(this).find('.knife-widget-button');

      initWidgetButton(input);
    });

    $(document).on('widget-added widget-updated', function(event, widget) {
      var input = widget.find('.knife-widget-button');

      initWidgetButton(input);
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


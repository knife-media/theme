(function($){
  function initColorPicker(widget) {
    widget.find('.color-picker').wpColorPicker( {
      change: _.throttle(function() {
        $(this).trigger('change');
      }, 3000),

      clear: _.throttle(function() {
          $(this).trigger('change');
      }, 4000)
    });
  }

  $(document).on('widget-added widget-updated', function(event, widget) {
    initColorPicker( widget );
  });

  $(document).ready(function() {
    $('#widgets-right .widget:has(.color-picker)').each(function () {
      initColorPicker($(this));
    });
  });


  $(document).on('change', '.knife-widget-taxonomy', function() {
    var list = $(this).closest('.widget-content').find('.knife-widget-termlist');

    var data = {
      action: 'knife_widget_terms',
      filter: jQuery(this).val(),
      nonce: knife_widget_handler.nonce
    }

    $.post(ajaxurl, data, function(response) {
      list.html(response);

      return list.show();
    });

    return list.hide();
  });



}(jQuery));


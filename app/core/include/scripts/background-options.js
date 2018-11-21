jQuery(document).ready(function($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var frame;

  var image = $('.knife-background-image');
  var color = $('.knife-background-color');


  function toggle() {
    var src = image.find('input').val();

    image.find('img').remove();
    image.find('button.remove').attr('disabled', 'disabled');
    image.find('select').attr('disabled', 'disabled');


    if(src.length > 0) {
      $('<img />', {src: src}).prependTo(image).css('max-width', '100%');

      image.find('button.remove').removeAttr('disabled');
      image.find('select').removeAttr('disabled');
    }
  }


  // Define color input as colorpicker
  color.find('input').wpColorPicker();


  // Process select image button click
  image.on('click', 'button.select', function(e) {
    e.preventDefault();

    if(frame) {
      return frame.open();
    }

    frame = wp.media({
      title: knife_background_options.choose,
      multiple: false
    });

    frame.on('select', function() {
      var attachment = frame.state().get('selection').first().toJSON();

      image.find('input').val(attachment.url);

      return toggle();
    });

    return frame.open();
  });


  // Delete image on link clicking
  image.on('click', 'button.remove', function(e) {
    e.preventDefault();

    image.find('input').val('');

    return toggle();
  });


  return toggle();
});

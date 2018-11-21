jQuery(document).ready(function($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-generator-box');


  // Add generator poster
  box.on('click', '.manage__poster', function(e) {
    e.preventDefault();

    var poster = box.find('.manage__poster');

    // Open default wp.media image frame
    var frame = wp.media({
      title: 'knife_random_generator',
      multiple: false
    });

    // on image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      poster.find('.manage__poster-media').val(selection.url);

      if(poster.find('img').length > 0) {
        return poster.find('img').attr('src', selection.url);
      }

      var showcase = $('<img />', {class: 'manage__poster-image', src: selection.url});

      return showcase.prependTo(poster);
    });

    return frame.open();
  });


  // Set text color input as colorpicker
  box.find('.manage__color input').wpColorPicker();
});

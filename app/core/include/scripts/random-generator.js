jQuery(document).ready(function($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-generator-box');


  // Add class for short time
  var dimmer = function(element, cl) {
    element.addClass(cl).delay(500).queue(function(){
      element.removeClass(cl).dequeue();
    });
  }


  // Add generator poster
  box.on('click', '.item__poster-figure', function(e) {
    e.preventDefault();

    var poster = $(this);

    // Open default wp.media image frame
    var frame = wp.media({
      title: 'knife_random_generator',
      multiple: false
    });

    // on image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      poster.find('.item__poster-media').val(selection.url);

      if(poster.find('img').length > 0) {
        return poster.find('img').attr('src', selection.url);
      }

      var showcase = $('<img />', {class: 'item__poster-image', src: selection.url});

      return showcase.prependTo(poster);
    });

    return frame.open();
  });


  // Add new item
  box.on('click', '.actions__add', function(e) {
    e.preventDefault();

    var last = box.find('.item').last();
    var copy = last.clone();

    // Clear input values
    copy.find('.item__option input').val('');
    copy.find('.item__option textarea').val('');

    // Destroy image tag
    copy.find('.item__poster-image').remove();
    copy.find('.item__poster-media').val('');

    last.after(copy);
  });


  // Remove item
  box.on('click', '.item .dashicons-trash', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }

    return item.remove();
  });


  // Generate button click
  box.on('click', '.item__poster-generate', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');
    var blank = item.find('.item__poster-blank');

    if(item.find('.item__poster-image').length < 1) {
      return dimmer(blank, 'item__poster-blank--error');
    }

    alert(1);
  });


  // Set text color input as colorpicker
  box.find('.manage__color input').wpColorPicker();
});

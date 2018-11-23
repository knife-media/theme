jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  if(typeof knife_random_generator === 'undefined') {
    return false;
  }

  var box = $('#knife-generator-box');


  // Add class for short time
  var blinkClass = function(element, cl) {
    element.addClass(cl).delay(500).queue(function(){
      element.removeClass(cl).dequeue();
    });
  }


  // Show loader
  var toggleLoader = function(item) {
    var spinner = item.find('.option__relative-spinner');

    spinner.toggleClass('is-active')

    if(spinner.hasClass('is-active')) {
      return item.find('.option__relative-generate').prop('disabled', true);
    }

    return item.find('.option__relative-generate').prop('disabled', false);
  }


  // Generate poster using ajax
  var createPoster = function(item) {
    var data = {
      'action': knife_random_generator.action,
      'nonce': knife_random_generator.nonce,
      'post_id': knife_random_generator.post,
      'caption': item.find('.option__general-caption').val(),
      'attachment': item.find('.option__relative-attachment').val()
    }

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      toggleLoader(item);

      if(answer.success && answer.data.length > 1) {
        item.find('.option__relative-media').val(answer.data);
        item.find('.option__relative-image').attr('src', answer.data);
      }
    });

    return toggleLoader(item);
  }


  // Add generator poster
  box.on('click', '.option__relative-poster', function(e) {
    e.preventDefault();

    var poster = $(this);

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_random_generator.choose,
      multiple: false
    });

    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      // Set hidden inputs values
      poster.find('.option__relative-media').val(selection.url);
      poster.find('.option__relative-attachment').val(selection.id);

      if(poster.find('img').length > 0) {
        return poster.find('img').attr('src', selection.url);
      }

      var showcase = $('<img />', {class: 'option__relative-image', src: selection.url});

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
    copy.find('.option__general-caption').val('');
    copy.find('.option__general-description').val('');

    // Destroy image tag
    copy.find('.option__relative-image').remove();
    copy.find('.option__relative-media').val('');
    copy.find('.option__relative-attachment').val('');

    last.after(copy);
  });


  // Remove item
  box.on('click', '.option .dashicons-trash', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }

    return item.remove();
  });


  // Generate button click
  box.on('click', '.option__relative-generate', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');
    var blank = item.find('.option__relative-blank');

    if(item.find('.option__relative-image').length < 1) {
      return blinkClass(blank, 'option__relative-blank--error');
    }

    return createPoster(item);
  });


  // Settings button click
  box.on('click', '.option__relative-settings', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    return item.find('.option--advanced').slideToggle();
  });


  // Set text color input as colorpicker
  box.find('.manage__color input').wpColorPicker();
});

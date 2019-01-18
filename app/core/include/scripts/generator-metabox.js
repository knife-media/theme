jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  if(typeof knife_generator_metabox === 'undefined') {
    return false;
  }

  var box = $('#knife-generator-box');


  /**
   * Add class for short time
   */
  function blinkClass(element, cl) {
    element.addClass(cl).delay(500).queue(function(){
      element.removeClass(cl).dequeue();
    });
  }


  /**
   * Show loader
   */
  function toggleLoader(item) {
    var spinner = item.find('.option__relative-spinner');

    spinner.toggleClass('is-active');

    return item.find('.option__relative-generate').prop('disabled',
      spinner.hasClass('is-active')
    );
  }


  /**
   * Generate poster using ajax
   */
  function createPoster(item) {
    var data = {
      'action': knife_generator_metabox.action,
      'nonce': knife_generator_metabox.nonce,
      'post_id': knife_generator_metabox.post_id,
      'caption': item.find('.option__general-caption').val(),
      'attachment': item.find('.option__relative-attachment').val()
    }

    item.find('.option__relative-warning').html('').hide();

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      if(answer.data.length > 1 && answer.success === true) {
        item.find('.option__relative-media').val(answer.data);
        item.find('.option__relative-image').attr('src', answer.data);

        return toggleLoader(item);
      }

      if(answer.data.length > 1 && answer.success === false) {
        item.find('.option__relative-warning').html(answer.data).show();

        return toggleLoader(item);
      }

      item.find('.option__relative-warning').html(knife_generator_metabox.error).show();

      return toggleLoader(item);
    });

    xhr.error(function() {
      toggleLoader(item);

      item.find('.option__relative-warning').html(knife_generator_metabox.error).show();
    });

    return toggleLoader(item);
  }


  /**
   * Add generator poster
   */
  box.on('click', '.option__relative-poster', function(e) {
    e.preventDefault();

    var poster = $(this);

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_generator_metabox.choose,
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


  /**
   * Add new item
   */
  box.on('click', '.actions__add', function(e) {
    e.preventDefault();

    var item = box.find('.item:first').clone();
    box.find('.item:last').after(item);

    return item.removeClass('item--hidden');
  });


  /**
   * Remove item
   */
  box.on('click', '.option .dashicons-trash', function(e) {
    e.preventDefault();

    $(this).closest('.item').remove();

    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }
  });


  /**
   * Generate button click
   */
  box.on('click', '.option__relative-generate', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');
    var blank = item.find('.option__relative-blank');

    if(item.find('.option__relative-image').length < 1) {
      return blinkClass(blank, 'option__relative-blank--error');
    }

    return createPoster(item);
  });


  /**
   * Settings button click
   */
  box.on('click', '.option__relative-settings', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    return item.find('.option--advanced').slideToggle();
  });


  /**
   * Set text color input as colorpicker
   */
  box.find('.manage__color input').wpColorPicker();


  /**
   * Show at least one item box on load
   */
  if(box.find('.item').length === 1) {
    box.find('.actions__add').trigger('click');
  }
});

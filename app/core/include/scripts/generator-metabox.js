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
    }

    // Add generator title if exists
    if($('#title').length > 0) {
      data.title = $('#title').val();
    }

    // Add required poster fields
    $.each(['heading', 'attachment', 'template'], function(i, v) {
      data[v] = item.find('[data-item="' + v + '"]').val();
    });

    // Clear warning before request
    var warning = item.find('.option__relative-warning');
    warning.html('').hide();

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      toggleLoader(item);

      if(typeof answer.data === 'undefined') {
        return warning.html(knife_generator_metabox.error).show();
      }

      answer.success = answer.success || false;

      if(answer.success === true && answer.data.length > 0) {
        item.find('[data-item="poster"]').val(answer.data);

        var poster = item.find('.option__relative-poster');

        // Create image if not exists
        if(poster.find('img').length === 0) {
          $('<img />').prependTo(poster);
        }

        return poster.find('img').attr('src', answer.data);
      }

      return warning.html(answer.data).show();
    });

    xhr.error(function() {
      toggleLoader(item);

      return warning.html(knife_generator_metabox.error).show();
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
      poster.find('[data-item="poster"]').val(selection.url);
      poster.find('[data-item="attachment"]').val(selection.id);

      if(poster.find('img').length === 0) {
        $('<img />').prependTo(poster);
      }

      poster.find('img').attr('src', selection.url);
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
    var caption = item.find('.option__relative-caption');

    if(item.find('.option__relative-poster > img').length < 1) {
      return blinkClass(caption, 'option__relative-caption--error');
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

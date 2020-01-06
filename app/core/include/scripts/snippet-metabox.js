jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-snippet-box');


  /**
   * Check required metabox options
   */
  if(typeof knife_snippet_metabox.error === 'undefined') {
    return false;
  }


  /**
   * Show generate loader
   */
  function toggleLoader() {
    box.find('.snippet__footer-spinner').toggleClass('is-active');
  }


  /**
   * Toggle snippet title on button click
   */
  box.on('click', '.snippet__footer-title', function(e) {
    e.preventDefault();

    box.find('.snippet__title').toggle();
  });


  /**
   * Add poster
   */
  box.on('click', '.snippet__poster', function(e) {
    var poster = $(this);

    // Create custom state
    var state = wp.media.controller.Library.extend({
      defaults: _.defaults({
        filterable: 'all',
      }, wp.media.controller.Library.prototype.defaults)
    });

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_snippet_metabox.choose || '',
      multiple: false,
      states: [
        new state()
      ]
    });

    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      // Set hidden input image url
      poster.find('.snippet__poster-image').val(selection.url);

      // Set hidden input attachment id
      poster.find('.snippet__poster-attachment').val(selection.id);

      if(poster.find('img').length === 0) {
        $('<img />').prependTo(poster);
      }

      poster.find('img').attr('src', selection.url);

      // Set snippet ready class
      box.find('.snippet').addClass('snippet--ready');
    });

    return frame.open();
  });


  /**
   * Remove poster
   */
  box.on('click', '.snippet__footer-delete', function(e) {
    e.preventDefault();

    var poster = box.find('.snippet__poster');

    // Remove hidden input image url
    poster.find('.snippet__poster-image').val('');

    // Remove hidden input attachment id
    poster.find('.snippet__poster-attachment').val('');

    // Remove image
    poster.find('img').remove();

    // Set snippet ready class
    box.find('.snippet').removeClass('snippet--ready');

    // Clear warning
    var warning = box.find('.snippet__warning');
    warning.html('').hide();
  });


  /**
   * Preview poster
   */
  box.on('click', '.snippet__poster-preview', function(e) {
    e.stopPropagation();

    var preview = box.find('img').attr('src');

    if(preview.length > 0) {
      window.open(preview, '_blank');
    }
  });


  /**
   * Generate poster
   */
  box.on('click', '.snippet__footer-generate', function(e) {
    e.preventDefault();

    var data = {
      'action': knife_snippet_metabox.action,
      'nonce': knife_snippet_metabox.nonce,
      'post_id': knife_snippet_metabox.post_id,
      'textbox': {}
    }

    if(box.find('.snippet__poster-attachment').val()) {
      data.attachment = box.find('.snippet__poster-attachment').val();
    }

    if(box.find('.snippet__title-textarea').val()) {
      data.textbox.title = box.find('.snippet__title-textarea').val();
    }

    // Clear warning before request
    var warning = box.find('.snippet__warning');
    warning.html('').hide();

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      toggleLoader();

      if(typeof answer.data === 'undefined') {
        return warning.html(knife_snippet_metabox.error).show();
      }

      answer.success = answer.success || false;

      if(answer.success === true) {
        var poster = box.find('.snippet__poster');

        // Set hidden input image url
        poster.find('.snippet__poster-image').val(answer.data);

        // Create image if not exists
        if(poster.find('img').length === 0) {
          $('<img />').prependTo(poster);
        }

        poster.find('img').attr('src', answer.data);

        // Set snippet ready class
        return box.find('.snippet').addClass('snippet--ready');
      }

      return warning.html(answer.data).show();
    });

    xhr.fail(function() {
      toggleLoader();

      return warning.html(knife_snippet_metabox.error).show();
    });

    return toggleLoader();
  });


  /**
   * Set snippet ready class
   */
  if(box.find('.snippet__poster img').length > 0) {
    box.find('.snippet').addClass('snippet--ready');
  }
});

jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  if(typeof knife_snippet_metabox === 'undefined') {
    return false;
  }

  var box = $('#knife-snippet-box');


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

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_snippet_metabox.choose,
      multiple: false
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
    }

    data.image = box.find('.snippet__poster-image').val();

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      var poster = box.find('.snippet__poster');

      if(answer.data.length > 0) {
        // Set hidden input image url
        poster.find('.snippet__poster-image').val(answer.data);

        // Create image if not exists
        if(poster.find('img').length === 0) {
          $('<img />').prependTo(poster);
        }

        poster.find('img').attr('src', answer.data);

        // Set snippet ready class
        box.find('.snippet').addClass('snippet--ready');
      }
    });
  });


  /**
   * Set snippet ready class
   */
  if(box.find('.snippet__poster img').length > 0) {
    box.find('.snippet').addClass('snippet--ready');
  }
});

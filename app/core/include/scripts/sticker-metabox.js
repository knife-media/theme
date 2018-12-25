jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var frame;
  var box = $("#knife-sticker-box");


  /**
   * Toggle spinner on upload
   */
  function toggleLoader() {
    box.find('.button').toggleClass('disabled');
    box.find('.spinner').toggleClass('is-active');

    box.find('.notice').remove();
  }


  /**
   * Show notice on upload
   */
  function showNotice(data) {
    var notice = $('<div />', {
      "class": "notice notice-error",
      "html": "<p>" + data + "</p>"
    });

    return notice.prependTo(box);
  }


  /**
   * Upload sticker action
   */
  box.on('click', '#knife-sticker-upload', function(e) {
    e.preventDefault();

    if(frame) {
      return frame.open();
    }

    frame = wp.media({
      title: knife_sticker_metabox.choose,
      multiple: false
    });

    frame.on('select', function() {
      var attachment = frame.state().get('selection').first().toJSON();

      var data = {
        action: 'knife_sticker_upload',
        post: box.data('post'),
        sticker: attachment.id
      }

      var xhr = $.ajax({method: 'POST', url: box.data('ajaxurl'), data: data}, 'json');

      xhr.done(function(answer) {
        toggleLoader();

        if(answer.success === false) {
          return showNotice(answer.data);
        }

        if(box.find('#knife-sticker-image').length > 0) {
          return box.find('#knife-sticker-image').attr('src', answer.data);
        }

        var img = $('<img />', {id: 'knife-sticker-image', src: answer.data});

        return img.prependTo(box);
      });

      return toggleLoader();
    });

    return frame.open();
  });


  /**
   * Delete sticker action
   */
  box.on('click' , '#knife-sticker-delete', function(e) {
    e.preventDefault();

    var data = {
      action: 'knife_sticker_delete',
      post: box.data('post')
    }

    var xhr = $.ajax({method: 'POST', url: box.data('ajaxurl'), data: data}, 'json');

    xhr.done(function(answer) {
      toggleLoader();

      if(answer.success === false) {
        return showNotice(answer.data);
      }

      return box.find('#knife-sticker-image').remove();
    });

    return toggleLoader();
  });
});

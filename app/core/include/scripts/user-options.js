jQuery(document).ready(function ($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var photo = $('#knife-user-photo');


  /**
   * Check required metabox options
   */
  if (typeof knife_user_options === 'undefined') {
    return false;
  }


  /**
   * Toggle background
   */
  function toggleBackground() {
    var input = photo.find('input.photo').val();

    photo.find('button.remove').addClass('hidden');
    photo.find('button.select').removeClass('hidden');

    // Remove image if exists
    photo.find('img').remove();

    if (input && input.length > 1) {
      photo.find('button.remove').removeClass('hidden');
      photo.find('button.select').addClass('hidden');

      // Append poster
      var poster = $('<img />', {
        'src': input
      });

      poster.prependTo(photo).css({
        'max-width': '100%',
        'border-radius': '2px',
        'box-shadow': '0 1px 1px rgba(0, 0, 0, .5)'
      });
    }
  }


  /**
   * Process select image button click
   */
  photo.on('click', 'button.select', function (e) {
    e.preventDefault();

    var frame = wp.media({
      title: knife_user_options.choose,
      multiple: false
    });

    var spinner = photo.find('span.spinner');

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();

      var data = {
        'action': knife_user_options.action,
        'nonce': knife_user_options.nonce,
        'user_id': knife_user_options.user_id,
        'poster': attachment.id
      }

      var xhr = $.ajax({
        method: 'POST',
        url: ajaxurl,
        data: data
      }, 'json');

      xhr.always(function (answer) {
        spinner.removeClass('is-active');

        if (typeof answer.data === 'undefined') {
          return alert(knife_user_options.error);
        }

        if (typeof answer.success === false) {
          return alert(answer.data);
        }

        photo.find('input.photo').val(answer.data);

        return toggleBackground();
      });

      return spinner.addClass('is-active');
    });

    frame.open();
  });


  /**
   * Delete image on link clicking
   */
  photo.on('click', 'button.remove', function (e) {
    e.preventDefault();

    photo.find('input.photo').val('');

    return toggleBackground();
  });


  /**
   * Launch toggle on load
   */
  return toggleBackground();
});
jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-distribute-box');


  /**
   * Check required metabox options
   */
  if(typeof knife_distribute_metabox === 'undefined') {
    return false;
  }


  /**
   * Check if error message exists
   */
  if(typeof knife_distribute_metabox.error === 'undefined') {
    return false;
  }


  /**
   * Set items proper name attribute
   */
  function sortItems() {
    if(typeof knife_distribute_metabox.meta_items === 'undefined') {
      return alert(knife_distribute_metabox.error);
    }

    var meta_items = knife_distribute_metabox.meta_items;

    box.find('.item:not(:first)').each(function(i) {
      var item = $(this);

      // Change fields name
      item.find('[data-item]').each(function() {
        var data = $(this).data('item');

        // Create name attribute
        var attr = meta_items + '[' + i + ']';
        var name = attr + '[' + data + ']';

        // Array names for checkboxes
        if($(this).is(':checkbox')) {
          name = name + '[]';
        }

        $(this).attr('name', name);
      });
    });
  }


  /**
   * Cancel scheduled event
   */
  function cancelTask(item, callback) {
    var data = {
      'action': knife_distribute_metabox.action,
      'nonce': knife_distribute_metabox.nonce,
      'post_id': knife_distribute_metabox.post_id
    }

    var spinner = item.find('.item__scheduled .spinner');

    // Add uniqid to data object
    data.uniqid = item.find('.item__uniqid').val();

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      spinner.removeClass('is-active');

      if(answer.success && typeof callback === 'function') {
        return callback();
      }

      var message = answer.data || knife_distribute_metabox.error;

      return alert(message);
    });

    xhr.error(function() {
      spinner.removeClass('is-active');

      return alert(knife_distribute_metabox.error);
    });

    return spinner.addClass('is-active');
  }


  /**
   * Remove item and create empty new if need
   */
  function removeItem(item) {
    item.remove();

    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }
  }


  /**
   * Add item poster
   */
  box.on('click', '.item__snippet-poster', function(e) {
    var poster = $(this);

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_distribute_metabox.choose,
      multiple: false
    });


    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      // Set hidden inputs values
      poster.find('[data-item="attachment"]').val(selection.id);

      // Set thumbnail as selection if exists
      if(typeof selection.sizes.thumbnail !== 'undefined') {
        selection = selection.sizes.thumbnail;
      }

      if(poster.find('img').length === 0) {
        $('<img />').prependTo(poster);
      }

      poster.find('img').attr('src', selection.url);
    });

    return frame.open();
  });


  /**
   * Update excerpt counter
   */
  box.on('keyup paste', '.item__snippet-excerpt', function() {
    var maxlength = $(this).data('maxlength');
    var counter = $(this).siblings('.item__snippet-counter');

    if(maxlength) {
      var value = $(this).val().length;

      if(value > 0) {
        return counter.text(maxlength - value).show();
      }
    }

    return counter.hide();
  });


  /**
   * Remove poster
   */
  box.on('click', '.item__snippet-delete', function(e) {
    e.stopPropagation();

    var poster = $(this).closest('.item__snippet-poster');

    // Remove hidden inputs values
    poster.find('[data-item="attachment"]').val('');

    // Remove image
    poster.find('img').remove();
  });


  /**
   * Update excerpt maxlength on targets check
   */
  box.on('change', '.item__targets-check input', function() {
    var item = $(this).closest('.item');
    var maxlength = 0;

    item.find('.item__targets input:checked').each(function() {
      if($(this).data('maxlength') > maxlength) {
        maxlength = $(this).data('maxlength');
      }
    });

    var excerpt = item.find('.item__snippet-excerpt');

    excerpt.data('maxlength', maxlength).trigger('keyup');
  });


  /**
   * Toggle time on delay date change
   */
  box.on('change', '.item__delay-date', function() {
    var item = $(this).closest('.item');

    // Toggle class depends on select value
    item.find('.item__delay-time').toggle(
        $(this).val().length > 0
    );
  });


  /**
   * Cancel scheduled event
   */
  box.on('click', '.item__scheduled-cancel', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    cancelTask(item, function() {
      item.find('.item__scheduled').remove();
    });
  });


  /**
   * Add new item
   */
  box.on('click', '.actions__add', function(e) {
    e.preventDefault();

    var item = box.find('.item:first').clone();

    // Insert after las item
    box.find('.item:last').after(item);

    // Update name attributes
    return sortItems();
  });


  /**
   * Remove item
   */
  box.on('click', '.item__delete', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    if(item.find('.item__scheduled').length === 0) {
      return removeItem(item);
    }

    cancelTask(item, function() {
      return removeItem(item);
    });
  });


  /**
   * Onload set up
   */
  (function() {
    // Add name attributes
    sortItems();

    // Show at least one item box on load
    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }

    // Show items
    box.find('.box--items').addClass('box--expand');
  })();
});

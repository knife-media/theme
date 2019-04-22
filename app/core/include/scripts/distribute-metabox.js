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

    $(this).closest('.item').remove();

    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }
  });


  /**
   * Show help text on delay change
   */
  box.on('change', '.item__delay-select', function(e) {
    var item = $(this).closest('.item');

    item.toggleClass('item--scheduled',
      parseInt($(this).val()) > 0
    );
  });


  /**
   * Onload set up
   */
  (function() {
    // Show at least one item box on load
    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }

    // Send delay select trigger event
    box.find('.item__delay-select').each(function() {
      $(this).trigger('change');
    });

    // Add name attributes
    return sortItems();
  })();
});

jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $("#knife-select-box");


  /**
   * Link input field
   */
  var input = box.find('.manage__input');


  /**
   * Check required metabox options
   */
  if(typeof knife_select_metabox === 'undefined') {
    return false;
  }


  /**
   * Show loader
   */
  function toggleLoader() {
    box.find('.manage__button').toggleClass('disabled');
    box.find('.manage__spinner').toggleClass('is-active');
  }


  /**
   * Add class for short time
   */
  function blinkClass(element, cl) {
    element.addClass(cl).delay(600).queue(function(){
      element.removeClass(cl).dequeue();
    });
  }


  /**
   * Append item
   */
  function appendItem(object) {
    var item = box.find('.item:first').clone();
    box.find('.item:first').after(item);

    if(object.hasOwnProperty('title')) {
      item.find('.item__title input').val(object.title);
    }

    if(object.hasOwnProperty('attachment') && object.attachment) {
      item.find('.item__poster-attachment').val(object.attachment);

      if(object.hasOwnProperty('poster') && object.poster) {
        showcase = $('<img />', {class: 'item__poster-image', src: object.poster});
        showcase.prependTo(item.find('.item__poster'));
      }
    }

    item.find('.item__link input').val(input.val());
    input.attr('value', '');

    // Update name attributes
    return sortItems();
  }


  /**
   * Set items proper name attribute
   */
  function sortItems() {
    if(typeof knife_select_metabox.meta_items === 'undefined') {
      return alert(knife_select_metabox.error);
    }

    var meta_items = knife_select_metabox.meta_items;

    box.find('.item:not(:first)').each(function(i) {
      var item = $(this);

      // Change fields name
      item.find('[data-item]').each(function() {
        var data = $(this).data('item');

        // Create name attribute
        var attr = meta_items + '[' + i + ']';
        var name = attr + '[' + data + ']';

        $(this).attr('name', name);
      });
    });
  }


  /**
   * Swap questions
   */
  box.on('click', '.item__change', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Don't swap first element
    if(item.index() === 1) {
      return false;
    }

    item.prev().insertAfter(item);

    // Update name attributes
    return sortItems();
  });


  /**
   * Add new item click
   */
  box.on('click', '.manage__append', function(e) {
    e.preventDefault();

    if(input.val().length === 0) {
      return blinkClass(input, 'manage__input--warning');
    }

    var data = {
      'action': knife_select_metabox.action,
      'nonce': knife_select_metabox.nonce,
      'link': input.val()
    }

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.always(function(answer, status) {
      toggleLoader();

      var object = {};

      if(status === 'success' && answer.success) {
        object = answer.data;
      }

      return appendItem(object);
    });

    return toggleLoader();
  });


  /**
   * Choose custom poster
   */
  box.on('click', '.item__poster', function(e) {
    var poster = $(this);

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_select_metabox.choose,
      multiple: false,
    });


    // On open frame select current attachment
    frame.on('open',function() {
      var selection = frame.state().get('selection');
      var attachment = poster.find('.item__poster-attachment').val();

      return selection.add(wp.media.attachment(attachment));
    });


    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      poster.find('.item__poster-attachment').val(selection.id);

      if(typeof selection.sizes.thumbnail !== 'undefined') {
        selection = selection.sizes.thumbnail;
      }

      if(poster.find('img').length > 0) {
        return poster.find('img').attr('src', selection.url);
      }

      var showcase = $('<img />', {class: 'item__poster-image', src: selection.url});

      return showcase.prependTo(poster);
    });

    return frame.open();
  });


  /**
   * Prevent sending post form on input enter
   */
  box.on('keypress', '.manage__input', function(e) {
    if(e.which == 13 || e.keyCode == 13) {
      e.preventDefault();

      return box.find('.manage__append').trigger('click');
    }
  });


  /**
   * Remove item
   */
  box.on('click', '.item__delete', function(e) {
    var item = $(this).closest('.item');

    return item.remove();
  });

  /**
   * Add name attributes on load
   */
    return sortItems();
});

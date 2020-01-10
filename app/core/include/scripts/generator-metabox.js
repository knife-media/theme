jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-generator-box');


  /**
   * Check required metabox options
   */
  if(typeof knife_generator_metabox === 'undefined') {
    return false;
  }


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
    var spinner = item.find('.item__image-spinner');

    spinner.toggleClass('is-active');

    return item.find('.item__image-generate').prop('disabled',
      spinner.hasClass('is-active')
    );
  }


  /**
   * Toggle manage options
   */
  function toggleManage(option) {
    // Get new answer class
    var toggle = 'item--' + option.data('manage');

    box.find('.item').toggleClass(toggle,
      option.is(':checked')
    );
  }


  /**
   * Set items proper name attribute
   */
  function sortItems() {
    if(typeof knife_generator_metabox.meta_items === 'undefined') {
      return alert(knife_generator_metabox.error);
    }

    var meta_items = knife_generator_metabox.meta_items;

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
   * Generate poster using ajax
   */
  function createPoster(item) {
    var data = {
      'action': knife_generator_metabox.action,
      'nonce': knife_generator_metabox.nonce,
      'post_id': knife_generator_metabox.post_id,
      'textbox': {}
    }

    // Add required poster fields
    $.each(['attachment', 'template'], function(i, v) {
      data[v] = item.find('[data-item="' + v + '"]').val();
    });

    data.textbox.title = $('#title').val() || '';

    if($('#knife-tagline-input').val()) {
      // If title not emnpty
      if(data.textbox.title) {
        data.textbox.title = data.textbox.title + ' ';
      }

      data.textbox.title = data.textbox.title + $('#knife-tagline-input').val();
    }

    // Add required poster fields
    $.each(['heading', 'description'], function(i, v) {
      data.textbox[v] = item.find('[data-item="' + v + '"]').val();
    });


    // Clear warning before request
    var warning = item.find('.item__image-warning');
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

        var poster = item.find('.item__image-poster');

        // Create image if not exists
        if(poster.find('img').length === 0) {
          $('<img />').prependTo(poster);
        }

        return poster.find('img').attr('src', answer.data);
      }

      return warning.html(answer.data).show();
    });

    xhr.fail(function() {
      toggleLoader(item);

      return warning.html(knife_generator_metabox.error).show();
    });

    return toggleLoader(item);
  }


  /**
   * Add generator poster
   */
  box.on('click', '.item__image-poster', function(e) {
    e.preventDefault();

    var poster = $(this);

    // Create custom state
    var state = wp.media.controller.Library.extend({
      defaults: _.defaults({
        filterable: 'all',
      }, wp.media.controller.Library.prototype.defaults)
    });


    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_generator_metabox.choose,
      multiple: false,
      states: [
        new state()
      ]
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
   * Manage checkbox answer trigger
   */
  box.on('change', '.manage input[data-manage]', function() {
    var option = $(this);

    return toggleManage(option);
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
   * Generate button click
   */
  box.on('click', '.item__image-generate', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');
    var caption = item.find('.item__image-caption');

    if(item.find('.item__image-poster > img').length < 1) {
      return blinkClass(caption, 'item__image-caption--error');
    }

    return createPoster(item);
  });


  /**
   * Set text color input as colorpicker
   */
  box.find('.manage__color input').wpColorPicker();


  /**
   * Onload set up
   */
  (function() {
    // Set manage classes
    box.find('.manage input[data-manage]').each(function() {
      var option = $(this);

      toggleManage(option);
    });

    // Show at least one item box on load
    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }

    // Show items
    box.find('.box--items').addClass('box--expand');

    // Add name attributes
    return sortItems();
  })();
});

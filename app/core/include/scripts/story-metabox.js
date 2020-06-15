jQuery(document).ready(function ($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-story-box');


  /**
   * Check required metabox options
   */
  if (typeof knife_story_metabox.error === 'undefined') {
    return false;
  }


  /**
   * Use this variable to store for current editor id
   */
  box.data('editor', 1);


  /**
   * Update wp.editor using editorId
   */
  function updateEditor(el) {
    if (typeof knife_story_metabox.editor === 'undefined') {
      knife_story_metabox.editor = 'html';
    }

    $.each(el.find('.wp-editor-area'), function (i, editor) {
      var textarea = $(editor);
      var editorId = textarea.attr('id');

      if (typeof editorId === 'undefined') {
        editorId = 'knife-story-editor-' + box.data('editor');
        textarea.attr('id', editorId);

        box.data().editor++;
      } else {
        wp.editor.remove(editorId);
      }

      wp.editor.initialize(editorId, {
        tinymce: {
          toolbar1: 'formatselect,link',
          invalid_styles: 'color font-weight font-size',
          block_formats: 'Paragraph=p;Heading 4=h4',
          init_instance_callback: function () {
            if (window.tinymce && window.switchEditors) {
              window.switchEditors.go(editorId, knife_story_metabox.editor);
            }
          }
        },
        quicktags: {
          buttons: 'link'
        },
        mediaButtons: false
      });
    });
  }


  /**
   * Add class for short time
   */
  function blinkClass(element, cl) {
    element.addClass(cl).delay(500).queue(function () {
      element.removeClass(cl).dequeue();
    });
  }


  /**
   * Display image
   */
  function displayImage(parent, link, image) {
    // Change src if image already exists
    if (parent.find('img').length > 0) {
      return parent.find('img').attr('src', link);
    }

    // Otherwise create new image
    var showcase = $('<img />', {
      class: image,
      src: link
    });

    return showcase.prependTo(parent);
  }


  /**
   * Update media image
   */
  function updateMedia(item) {
    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_story_metabox.choose,
      multiple: false
    });

    // On open frame select current attachment
    frame.on('open', function () {
      var selection = frame.state().get('selection');
      var attachment = item.find('.item__media').val();

      return selection.add(wp.media.attachment(attachment));
    });

    // On image select
    frame.on('select', function () {
      var selection = frame.state().get('selection').first().toJSON();

      item.find('.item__media').val(selection.id);

      if (typeof selection.sizes.thumbnail !== 'undefined') {
        selection = selection.sizes.thumbnail;
      }

      // Show preview
      displayImage(item, selection.url, 'item__image');
    });

    return frame.open();
  }


  /**
   * Delete or clear item
   */
  box.on('click', '.item__field-trash', function (e) {
    e.preventDefault();

    $(this).closest('.item').remove();
  });


  /**
   * Add image to item
   */
  box.on('click', '.item__image', function (e) {
    e.preventDefault();

    var item = $(this).closest('.item');
    return updateMedia(item);
  });


  /**
   * Update item image on click
   */
  box.on('click', '.item__field-image', function (e) {
    e.preventDefault();

    var item = $(this).closest('.item');
    return updateMedia(item);
  });


  /**
   * Remove item image on click
   */
  box.on('click', '.item__field-clear', function (e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Clear input values
    item.find('.item__media').val('');

    // Destroy image tag
    item.find('.item__image').remove();
  });


  /**
   * Add story background
   */
  box.on('click', '.manage__background', function (e) {
    e.preventDefault();

    var background = box.find('.manage__background');

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_story_metabox.choose,
      multiple: false
    });

    // On image select
    frame.on('select', function () {
      var selection = frame.state().get('selection').first().toJSON();

      background.find('.manage__background-media').val(selection.url);

      // Show preview
      displayImage(background, selection.url, 'manage__background-image');

      // Set shadow on image creation
      box.find('.manage__range').trigger('change');
    });

    return frame.open();
  });


  /**
   * Set items proper name attribute
   */
  function sortItems(callback) {
    if (typeof knife_story_metabox.meta_items === 'undefined') {
      return alert(knife_story_metabox.error);
    }

    var meta_items = knife_story_metabox.meta_items;

    box.find('.item:not(:first)').each(function (i) {
      var item = $(this);

      // Change fields name
      item.find('[data-item]').each(function () {
        var data = $(this).data('item');

        // Create name attribute
        var attr = meta_items + '[' + i + ']';
        var name = attr + '[' + data + ']';

        $(this).attr('name', name);
      });
    });

    if (typeof callback === 'function') {
      return callback();
    }
  }


  /**
   * Add new item
   */
  box.on('click', '.actions__add', function (e) {
    e.preventDefault();

    var item = box.find('.item:first').clone();

    // Insert after last item
    box.find('.item:last').after(item);

    sortItems(function () {
      updateEditor(item);
    });
  });


  /**
   * Shadow range
   */
  box.on('change', '.manage__item-shadow', function (e) {
    var blank = box.find('.manage__background-blank');
    var shade = parseInt($(this).val()) / 100;

    if (box.find('.manage__background-image').length < 1) {
      return blinkClass(blank, 'manage__background-blank--error');
    }

    blank.css('background-color', 'rgba(0, 0, 0, ' + shade + ')');
  });


  /**
   * Blur range
   */
  box.on('change', '.manage__item-blur', function (e) {
    var blank = box.find('.manage__background-blank');

    if (box.find('.manage__background-image').length < 1) {
      return blinkClass(blank, 'manage__background-blank--error');
    }

    var image = box.find('.manage__background-image');

    image.css('filter', 'blur(' + $(this).val() + 'px)');
  });


  /**
   * Set text color input as colorpicker
   */
  box.find('.manage__item-color').wpColorPicker();


  /**
   * Onload set up
   */
  (function () {
    // Sort items and update editor
    sortItems(function () {
      box.find('.item:not(:first)').each(function (i) {
        var item = $(this);

        // Update item editor
        updateEditor(item);
      });
    });


    // Set shadow range
    box.find('.manage__item-shadow').trigger('change');

    // Set blur range
    box.find('.manage__item-blur').trigger('change');

    // Show items
    box.find('.box--items').addClass('box--expand');
  })();
});
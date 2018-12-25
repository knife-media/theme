jQuery(document).ready(function($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-story-box');

  /**
   * Use this variable as storage for current editor id
   */
  box.data('editor', 1);


  /**
   * Sort items
   */
  box.sortable({
    items: '.item',
    handle: '.item__field-drag',
    placeholder: 'dump',
  }).disableSelection();


  /**
   * Create wp.editor using item element
   */
  function createEditor(el) {
    var textarea = el.find('.item__entry');
    var editorId = textarea.attr('id');

    if(typeof editorId === 'undefined') {
      editorId = 'knife-story-editor-' + box.data('editor');
      textarea.attr('id', editorId);

      box.data().editor++;
    } else {
      wp.editor.remove(editorId);
    }

    wp.editor.initialize(editorId, {
      tinymce: true,
      quicktags: true,
      mediaButtons: false,
      tinymce: {
        toolbar1: 'formatselect,bold,italic,bullist,numlist,link',
        block_formats: 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4'
      }
    });
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
   * Display image
   */
  function displayImage(parent, link, image) {
    // Change src if image already exists
    if(parent.find('img').length > 0) {
      return parent.find('img').attr('src', link);
    }

    // Otherwise create new image
    var showcase = $('<img />', {class: image, src: link});

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
    frame.on('open',function() {
      var selection = frame.state().get('selection');
      var attachment = item.find('.item__media').val();

      return selection.add(wp.media.attachment(attachment));
    });

    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      item.find('.item__media').val(selection.id);

      if(typeof selection.sizes.thumbnail !== 'undefined') {
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
  box.on('click', '.item__field-trash', function(e) {
    e.preventDefault();

    $(this).closest('.item').remove();

    if(box.find('.item').length === 1) {
      box.find('.actions__add').trigger('click');
    }
  });


  /**
   * Add image to item
   */
  box.on('click', '.item__image', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');
    return updateMedia(item);
  });


  /**
   * Update item image on click
   */
  box.on('click', '.item__field-image', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');
    return updateMedia(item);
  });


  /**
   * Remove item image on click
   */
  box.on('click', '.item__field-clear', function(e) {
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
  box.on('click', '.option__background', function(e) {
    e.preventDefault();

    var background = box.find('.option__background');

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_story_metabox.choose,
      multiple: false
    });

    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      background.find('.option__background-media').val(selection.url);

      // Show preview
      displayImage(background, selection.url, 'option__background-image');

      // Set shadow on image creation
      box.find('.option__range').trigger('change');
    });

    return frame.open();
  });


  /**
   * Add new item
   */
  box.on('click', '.actions__add', function(e) {
    e.preventDefault();

    var item = box.find('.item:first').clone();

    item.removeClass('item--hidden');
    box.find('.item:last').after(item);

    return createEditor(item);
  });


  /**
   * Shadow range
   */
  box.on('change', '.option__range--shadow', function(e) {
    var blank = box.find('.option__background-blank');
    var shade = parseInt($(this).val()) / 100;

    if(box.find('.option__background-image').length < 1) {
      return blinkClass(blank, 'option__background-blank--error');
    }

    blank.css('background-color', 'rgba(0, 0, 0, ' + shade + ')');
  });


  /**
   * Blur range
   */
  box.on('change', '.option__range--blur', function(e) {
    var blank = box.find('.option__background-blank');

    if(box.find('.option__background-image').length < 1) {
      return blinkClass(blank, 'option__background-blank--error');
    }

    var image = box.find('.option__background-image');

    image.css('filter', 'blur(' + $(this).val() + 'px)');
  });



  /**
   * Reinit wp editor on drag
   */
  box.on('sortstop', function(event, ui) {
    return createEditor(ui.item);
  });


  /**
   * Init wp editors
   */
  box.find('.item').each(function(i, el) {
    var item = $(this);

    if(!item.hasClass('item--hidden')) {
      createEditor(item);
    }
  });


  /**
   * Set background range on load
   */
  box.find('.option__range').trigger('change');


  /**
   * Show at least one item box on load
   */
  if(box.find('.item').length === 1) {
    box.find('.actions__add').trigger('click');
  }
});

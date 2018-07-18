jQuery(document).ready(function($) {
  if (typeof wp.media === 'undefined') return;

  var box = $('#knife-story-box');

  // use this variable as storage for current editor id
  box.data('items', 0);


  // sort items
  box.sortable({
    items: '.item',
    handle: '.item__field-drag',
    placeholder: 'dump',
  }).disableSelection();


  // clear item
  var clear = function() {
    var item = box.find('.item').first().clone()

    // clear input values
    item.find('.item__entry').val('');
    item.find('.item__media').val('');

    // destroy image tag
    item.find('.item__image').remove();

    return item;
  }


  // create virtual item element
  var dummy = clear();


  // create wp.editor using item element
  var editor = function(el) {
    var text = el.find('.item__entry');
    var edit = text.attr('id');

    if(typeof edit === 'undefined') {
      edit = 'knife-story-text-' + box.data('items');
      text.attr('id', edit);

      box.data().items++;
    }
    else {
      wp.editor.remove(edit);
    }

    wp.editor.initialize(edit, {
      tinymce: true,
      quicktags: true,
      mediaButtons: false,
      tinymce: {
        toolbar1: 'formatselect,bold,italic,bullist,numlist,link',
        block_formats: 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4'
      }
    });
  }


  // add class for short time
  var dimmer = function(element, cl) {
    element.addClass(cl).delay(500).queue(function(){
      element.removeClass(cl).dequeue();
    });
  }


  // display image
  var display = function(parent, link, image) {
    // change src if image already exists
    if(parent.find('img').length > 0)
      return parent.find('img').attr('src', link);

    // otherwise create new image
    var showcase = $('<img />', {class: image, src: link});

    return showcase.prependTo(parent);
  }


  // set image shadow on load
  var shadow = function(cl) {
    var blank = box.find('.option__background-blank');
    var shade = parseInt(box.find(cl).val()) / 100;

    blank.css('background-color', 'rgba(0, 0, 0, ' + shade + ')');
  }


  var media = function(item) {
    // open default wp.media image frame
    var frame = wp.media({
      title: knife_story_manager.choose,
      multiple: false
    });

    // on open frame select current attachment
    frame.on('open',function() {
      var selection = frame.state().get('selection');
      var attachment = item.find('.item__media').val();

      return selection.add(wp.media.attachment(attachment));
    });

    // on image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      item.find('.item__media').val(selection.id);

      if(typeof selection.sizes.thumbnail !== 'undefined')
        selection = selection.sizes.thumbnail;

      // show preview
      display(item, selection.url, 'item__image');
    });

    return frame.open();
  }


  // delete or clear item
  box.on('click', '.item__field-trash', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    if(box.find('.item').length === 1)
      box.find('.actions__add').trigger('click');

    return item.remove();
  });


  // add image to item
  box.on('click', '.item__image', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    return media(item);
  });


  // update item image on click
  box.on('click', '.item__field-image', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    return media(item);
  });


  // remove item image on click
  box.on('click', '.item__field-clear', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // clear input values
    item.find('.item__media').val('');

    // destroy image tag
    item.find('.item__image').remove();
  });


  // add story background
  box.on('click', '.option__background', function(e) {
    e.preventDefault();

    var background = box.find('.option__background');

    // open default wp.media image frame
    var frame = wp.media({
      title: knife_story_manager.choose,
      multiple: false
    });

    // on image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      background.find('.option__background-media').val(selection.url);

      // show preview
      display(background, selection.url, 'option__background-image');

      // set shadow on image creation
      box.find('.option__range').trigger('change');
    });

    return frame.open();
  });


  // add new item
  box.on('click', '.actions__add', function(e) {
    e.preventDefault();

    var last = box.find('.item').last(),
      copy = dummy.clone();

    last.after(copy);

    return editor(copy);
  });


  // shadow range
  box.on('change', '.option__range--shadow', function(e) {
    var blank = box.find('.option__background-blank');
    var shade = parseInt($(this).val()) / 100;

    if(box.find('.option__background-image').length < 1)
      return dimmer(blank, 'option__background-blank--error');

    blank.css('background-color', 'rgba(0, 0, 0, ' + shade + ')');
  });


  // blur range
  box.on('change', '.option__range--blur', function(e) {
    var blank = box.find('.option__background-blank');

    if(box.find('.option__background-image').length < 1)
      return dimmer(blank, 'option__background-blank--error');

    var image = box.find('.option__background-image');

    image.css('filter', 'blur(' + $(this).val() + 'px)');
  });



  // reinit wp editor on drag
  box.on("sortstop", function(event, ui) {
    return editor(ui.item);
  });


  // init wp editors
  box.find('.item').each(function(i, el) {
    return editor($(this));
  });


  return box.find('.option__range').trigger('change');
});

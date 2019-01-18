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
   * Sort items
   */
  box.find('.box--items').sortable({
    items: '.item',
    handle: '.dashicons-menu',
    placeholder: 'dump',
    axis: 'y'
  }).disableSelection();


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
      item.find('.option__title').val(object.title);
    }

    if(object.hasOwnProperty('attachment') && object.attachment) {
      item.find('.option__attachment').val(object.attachment);

      if(object.hasOwnProperty('poster') && object.poster) {
        showcase = $('<img />', {class: 'option__image', src: object.poster});
        showcase.prependTo(item.find('.option--poster'));
      }
    }

    item.find('.option__link').val(input.val());
    input.attr('value', '');
  }


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
  box.on('click', '.option--poster', function(e) {
    var poster = $(this);

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_select_metabox.choose,
      multiple: false
    });


    // On open frame select current attachment
    frame.on('open',function() {
      var selection = frame.state().get('selection');
      var attachment = poster.find('.option__attachment').val();

      return selection.add(wp.media.attachment(attachment));
    });


    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      poster.find('.option__attachment').val(selection.id);

      if(typeof selection.sizes.thumbnail !== 'undefined') {
        selection = selection.sizes.thumbnail;
      }

      if(poster.find('img').length > 0) {
        return poster.find('img').attr('src', selection.url);
      }

      var showcase = $('<img />', {class: 'option__image', src: selection.url});

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
  box.on('click', '.item .dashicons-trash', function(e) {
    var item = $(this).closest('.item');

    return item.remove();
  });
});

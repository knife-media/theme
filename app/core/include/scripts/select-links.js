jQuery(document).ready(function($) {
  var box = $("#knife-select-box");

  var set = {
    'link': box.find('.input-link'),
    'text': box.find('.input-text'),
    'button': box.find('.button-append'),
    'loader': box.find('.spinner')
  }

  // Sort items
  box.find('.knife-select-items').sortable({
    items: '.knife-select-item',
    handle: '.dashicons-menu',
    placeholder: 'knife-select-dump',
    axis: 'y'
  }).disableSelection();


  // Show loader
  var toggleLoader = function() {
    set.button.toggleClass('disabled');
    set.loader.toggleClass('is-active');
  }

  // Add class for short time
  var blinkClass = function(element, cl) {
    element.addClass(cl).delay(600).queue(function(){
      element.removeClass(cl).dequeue();
    });
  }

  // Get post title by link
  var getTitle = function(link) {
    var data = {
      'action': box.data('action'),
      'nonce': box.data('nonce'),
      'link': link
    }

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      toggleLoader();

      if(answer.success && answer.data.length > 1) {
        set.text.val(answer.data);

        return set.button.trigger('click');
      }

      return blinkClass(set.text, 'warning');
    });

    xhr.error(function() {
      toggleLoader();

      return blinkClass(set.text, 'warning');
    });

    return toggleLoader();
  }


  // Append item
  var appendItem = function(link, text) {
    var item = box.find('.knife-select-item:first').clone();

    item.find('p.item-text').html(text);
    item.find('input.item-text').val(text);

    item.find('a.item-link').html(link).attr('href', link);
    item.find('input.item-link').val(link);

    box.find('.knife-select-items').append(item);

    return item.removeClass('hidden');
  }


  // Add new item click
  box.on('click', '.knife-select-manage .button', function(e) {
    e.preventDefault();

    var input = {
      'link': set.link.val(),
      'text': set.text.val()
    }

    if(input.link.length < 1) {
      return blinkClass(set.link, 'warning');
    }

    if(input.text.length < 1) {
      return getTitle(input.link);
    }

    set.link.val('');
    set.text.val('');

    return appendItem(input.link, input.text);
  });


  // Prevent sending post form on input enter
  box.on('keypress', '.knife-select-manage input', function(e) {
    if (e.which == 13 || e.keyCode == 13) {
      e.preventDefault();

      return set.button.trigger('click');
    }
  });


  // Remove item
  box.on('click', '.knife-select-items .dashicons-trash', function(e) {
    var item = $(this).closest('.knife-select-item');

    return item.remove();
  });
});

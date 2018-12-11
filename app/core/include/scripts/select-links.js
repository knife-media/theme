jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $("#knife-select-box");


  // Sort items
  box.find('.box--items').sortable({
    items: '.item',
    handle: '.dashicons-menu',
    placeholder: 'dump',
    axis: 'y'
  }).disableSelection();


  // Show loader
  var toggleLoader = function() {
    box.find('.option__button').toggleClass('disabled');
    box.find('.option .spinner').toggleClass('is-active');
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
      console.log(answer);
      return;

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
    var item = box.find('.item:first').clone();

    item.find('p.item-text').html(text);
    item.find('input.item-text').val(text);

    item.find('a.item-link').html(link).attr('href', link);
    item.find('input.item-link').val(link);

    box.find('.knife-select-items').append(item);

    return item.removeClass('hidden');
  }


  // Add new item click
  box.on('click', '.option__button--append', function(e) {
    e.preventDefault();

    var options = {
      link: box.find('.option__input--link'),
      title: box.find('.option__input--title')
    }

    if(options.link.val().length < 1) {
      return blinkClass(options.link, 'option__input--warning');
    }

    if(options.title.val().length < 1) {
      return getTitle(options.title);
    }

    options.link.val('');
    options.title.val('');

    return appendItem(options);
  });


  // Prevent sending post form on input enter
  box.on('keypress', '.option__input', function(e) {
    if (e.which == 13 || e.keyCode == 13) {
      e.preventDefault();

      return box.find('.option__button--append').trigger('click');
    }
  });


  // Remove item
  box.on('click', '.item .dashicons-trash', function(e) {
    var item = $(this).closest('.item');

    return item.remove();
  });
});

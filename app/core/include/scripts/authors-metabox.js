jQuery(document).ready(function ($) {
  if (typeof knife_authors_metabox.error === 'undefined') {
    return false;
  }

  var box = $('#knife-authors-box');

  /**
   * Skip if no action
   */
  if (typeof knife_authors_metabox.action === 'undefined') {
    return false;
  }


  /**
   * Skip if no post meta
   */
  if (typeof knife_authors_metabox.meta === 'undefined') {
    return false;
  }


  /**
   * Find form input
   */
  var input = box.find('.authors-input');


  /**
   * Action to create new author
   */
  function createAuthor(author) {
    var verify = knife_authors_metabox.verify || '';

    if (!confirm(verify)) {
      return false
    }

    var data = {
      'action': knife_authors_metabox.action,
      'nonce': knife_authors_metabox.nonce || '',
      'author': author
    }

    // Disable input
    input.prop('readonly', true);

    var xhr = $.ajax({
      method: 'POST',
      url: ajaxurl,
      data: data
    }, 'json');

    xhr.done(function (answer) {
      input.prop('readonly', false);

      if (answer.success) {
        return appendAuthor(answer.data.id, answer.data.author);
      }

      var message = answer.data || knife_authors_metabox.error;

      // Show error message
      return alert(message);
    });

    xhr.fail(function () {
      input.prop('readonly', false);

      return alert(knife_authors_metabox.error);
    });
  }


  /**
   * Append author to list
   */
  function appendAuthor(id, author) {
    // Try to find clone
    var find = box.find('.authors-item input[value="' + id + '"]');

    if (find.length > 0) {
      find.closest('.authors-item').remove();
    }

    var meta = knife_authors_metabox.meta + '[]';

    // Create item element
    var item = $('<p/>', {
      'class': 'authors-item'
    });
    item.html(author);
    item.appendTo(box);

    var span = $('<span/>', {
      'class': 'authors-delete'
    });
    span.prependTo(item);

    // Create user input
    var user = $('<input/>', {
      'type': 'hidden',
      'name': meta
    });
    user.val(id);
    user.prependTo(item);
  }


  /**
   * On input change
   */
  function selectAuthor() {
    var author = this.value.split(':', 2);

    // Clear input
    input.val('');

    if (author.length < 2) {
      return false;
    }

    if (author[0].indexOf('+') === 0) {
      return createAuthor(author[1]);
    }

    // Append author to list
    appendAuthor(author[0], author[1]);
  }


  /**
   * Use suggest jQuery plugin on authors input
   */
  var options = {
    resultsClass: 'knife-authors-suggest',
    selectClass: 'knife-authors-select',
    matchClass: 'knife-authors-match',
    onSelect: selectAuthor
  }


  /**
   * Item remove handler
   */
  box.on('click', '.authors-delete', function () {
    $(this).closest('.authors-item').remove();
  });


  /**
   * Suggest on input
   */
  input.suggest(ajaxurl + '?action=' + knife_authors_metabox.action, options);
});
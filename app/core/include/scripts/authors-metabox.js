jQuery(document).ready(function($) {
  if(typeof knife_authors_metabox.error === 'undefined') {
    return false;
  }

  var box = $('#knife-authors-box');

  /**
   * Skip if no action
   */
  if(typeof knife_authors_metabox.action === 'undefined') {
    return false;
  }


  /**
   * Skip if no post meta
   */
  if(typeof knife_authors_metabox.post_meta === 'undefined') {
    return false;
  }


  /**
   * Find form input
   */
  var input = box.find('.authors-input');


  /**
   * On input change
   */
  function selectAuthor() {
    var author = this.value.split(':', 2);

    if(author.length < 2) {
      return false;
    }

    var meta = knife_authors_metabox.post_meta + '[]';

    // Create item element
    var item = $('<p/>', {'class': 'authors-item'});
    item.html(author[1]);
    item.appendTo(box);

    // Create user input
    var user = $('<input/>', {'type': 'hidden', 'name': meta});
    user.val(author[0]);
    user.appendTo(item);

    return input.val('');
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
  box.on('click', '.authors-item', function() {
    return $(this).remove();
  });


  /**
   * Suggest on input
   */
  input.suggest(ajaxurl + '?action=' + knife_authors_metabox.action, options);
});

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
   * Find form input
   */
  var input = box.find('.authors-input');


  /**
   * On input change
   */
  function selectAuthor() {
    console.log(this.value);
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
   * Suggest on input
   */
  input.suggest(ajaxurl + '?action=' + knife_authors_metabox.action, options);
});

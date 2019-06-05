/**
 * Smart display entry widgets
 *
 * @since 1.9
 */

(function() {
  var post = document.querySelector('.entry-content');


  /**
   * Check if entry-content exists and it's long enough
   */
  if(post === null || post.children.length < 8) {
    return false;
  }


  var widgets = document.querySelector('.entry-widgets');

  /**
   * Check if entry-widgets block has at least one widget
   */
  if(widgets === null || widgets.firstElementChild === null) {
    return false;
  }


  /**
   * Try to move first widget to content middle
   */
  var middle = Math.floor(post.children.length / 2);

  for(var i = middle; i < post.children.length; i++) {
    var relative = post.children[i];

    if(relative.tagName.toLowerCase() === 'p') {
      var figure = document.createElement('figure');

      figure.classList.add('figure', 'figure--widget');
      figure.appendChild(widgets.firstElementChild);

      post.insertBefore(figure, relative);

      break;
    }
  }


  /**
   * Show entry-widgets block or remove if empty
   */
  if(widgets.children.length > 0) {
    return widgets.classList.add('entry-widgets--expand')
  }

  return widgets.parentNode.removeChild(widgets);
})();

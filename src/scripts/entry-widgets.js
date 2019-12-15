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
  if(post === null) {
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
   * Find post middle
   */
  var middle = Math.floor(post.children.length / 3);


  /**
   * Try to move first widget to content middle
   */
  if(post.children.length > 8) {
    var allowed = ['p', 'blockquote'];

    for(var i = middle; i < post.children.length; i++) {
      var relative = post.children[i];

      // Check if next tag in allowed list
      if(allowed.indexOf(relative.tagName.toLowerCase()) < 0) {
        continue;
      }

      if(typeof post.children[i - 1] === 'undefined') {
        continue;
      }

      var following = post.children[i - 1];

      // Check if prev tag in allowed list
      if(allowed.indexOf(following.tagName.toLowerCase()) < 0) {
        continue;
      }

      // Create aside
      var aside = document.createElement('aside');
      aside.classList.add('aside', 'aside--widget');
      aside.appendChild(widgets.firstElementChild);

      post.insertBefore(aside, relative);
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

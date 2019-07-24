/**
 * Append similar posts to single template
 *
 * @since 1.5
 * @version 1.9
 */

(function() {
  var post = document.querySelector('.entry-content');


  /**
   * Check if similar posts exist
   */
  if(typeof knife_similar_posts === 'undefined' || knife_similar_posts.similar === 'undefuned') {
    return false;
  }


  /**
   * Shuffle similar posts array
   */
  var similar = (function(items) {
    for(var i = 0, c; i < items.length; i++) {
        c = Math.floor(Math.random() * i);

        var temp = items[i];
        items[i] = items[c];
        items[c] = temp;
    }

    return items;
  }(knife_similar_posts.similar));


  /**
   * Create similar block
   */
  function appendSimilar(following) {
    var wrap = document.createElement('figure');
    wrap.classList.add('figure', 'figure--similar');

    var title = document.createElement('h4');
    title.innerHTML = knife_similar_posts.title || '';
    wrap.appendChild(title);

    // Add similar items
    for(var i = 0; i < similar.length && i < 3; i++) {
      if(!similar[i].link || !similar[i].title) {
        continue;
      }

      var item = document.createElement('p');
      wrap.appendChild(item);

      var link = document.createElement('a');
      link.href = similar[i].link;
      link.innerHTML = similar[i].title;
      item.appendChild(link);
    }

    following.parentNode.insertBefore(wrap, following.nextSibling);
  }


  /**
   * Create auto similar posts if need
   */
  (function() {
    // Check if entry-content exists and it's long enough
    if(post === null || post.children.length < 10) {
      return false;
    }

    // It will be better to skip cards posts
    if(document.body.classList.contains('is-chat')) {
      return false;
    }

    // Find start point
    var landmark = Math.floor(post.children.length / 1.5);

    // Check if manual similar alread exists
    if(post.querySelector('.figure--similar') !== null) {
      return false;
    }

    var allowed = ['p', 'blockquote'];

    for(var i = landmark; i < post.children.length - 5; i++) {
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

      appendSimilar(following);
      break;
    }
  })();


  /**
   * Append gtm attributes to all similar blocks
   */
  (function() {
    // Check if action exists
    if(typeof knife_similar_posts.action === 'undefined') {
      return false;
    }

    var figure = post.querySelectorAll('.figure--similar') || [];

    for(var i = 0; i < figure.length; i++) {
      var items = figure[i].querySelectorAll('a');

      // Set attributes for all similar links
      for(var e = 0; e < items.length; e++) {
        items[e].setAttribute('data-action', knife_similar_posts.action);

        if(items[e].href) {
          items[e].setAttribute('data-label', items[e].href);
        }
      }
    }
  })();
})();

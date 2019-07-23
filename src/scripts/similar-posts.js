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
  if(typeof knife_similar_posts === 'undefined') {
    return false;
  }


  /**
   * Check if entry-content exists and it's long enough
   */
  if(post === null || post.children.length < 10) {
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
  }(knife_similar_posts));


  /**
   * Create similar block
   */
  function appendSimilar(relative, similar) {
    if(typeof similar.link === 'undefined' || typeof similar.title === 'undefined') {
      return false;
    }

    var wrap = document.createElement('figure');
    wrap.classList.add('figure', 'figure--similar');

    // Append head
    (function(){
      if(typeof similar.head === 'undefined') {
        return false;
      }

      var head = document.createElement('h4');
      head.innerHTML = similar.head;
      wrap.appendChild(head);
    })();

    var item = document.createElement('p');
    wrap.appendChild(item);

    var link = document.createElement('a');
    link.href = similar.link;
    link.innerHTML = similar.title;
    item.appendChild(link);

    // Append gtm action to similar link
    (function() {
      if(typeof similar.action === 'undefined') {
        return false;
      }

      link.setAttribute('data-action', similar.action);
    })();

    // Append gtm label to similar link
    (function() {
      if(typeof similar.label === 'undefined') {
        return false;
      }

      link.setAttribute('data-label', similar.label);
    })();

    relative.parentNode.insertBefore(wrap, relative.nextSibling);
  }


  /**
   * Append 2 similar posts
   */
  if(post.querySelector('.figure--similar') !== null) {
    return;
  }

  var range = Math.floor(post.children.length / 3);

  for(var i = 0; i < 2; i++) {
    if(typeof similar[i] === 'undefined') {
      continue;
    }

    var allowed = ['p', 'blockquote'];

    for(var e = range; e < post.children.length - 5; e++) {
      var relative = post.children[e];

      // Check if next tag in allowed list
      if(allowed.indexOf(relative.tagName.toLowerCase()) < 0) {
        continue;
      }

      if(typeof post.children[e - 1] === 'undefined') {
        continue;
      }

      var following = post.children[e - 1];

      // Check if prev tag in allowed list
      if(allowed.indexOf(following.tagName.toLowerCase()) < 0) {
        continue;
      }

      appendSimilar(following, similar[i]);

      break;
    }

    range = Math.floor(post.children.length / 3) + e;
  }
})();

/**
 * Append similar posts to single template
 *
 * @since 1.5
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
  var appendSimilar = function(relative, similar) {
    if(typeof similar.link === 'undefined' || typeof similar.title === 'undefined') {
      return false;
    }

    var wrap = document.createElement('figure');
    wrap.classList.add('similar');

    var item = document.createElement('div');
    item.classList.add('similar__item');
    wrap.appendChild(item);

    // Append emoji
    (function(){
      if(typeof similar.emoji === 'undefined') {
        return false;
      }

      var emoji = document.createElement('div');
      emoji.classList.add('similar__emoji');
      emoji.innerHTML = similar.emoji;
      wrap.appendChild(emoji);
    })();

    // Append head
    (function(){
      if(typeof similar.head === 'undefined') {
        return false;
      }

      var head = document.createElement('div');
      head.classList.add('similar__item-head');
      head.innerHTML = similar.head;
      item.appendChild(head);
    })();

    var link = document.createElement('a');
    link.classList.add('similar__item-link');
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
  var range = Math.floor(post.children.length / 3);

  for(var i = 0, start = 0; i < 2; i++) {
    if(typeof similar[i] === 'undefined') {
      continue;
    }

    for(var e = range; e < post.children.length - 5; e++) {
      var relative = post.children[e];

      if(relative.tagName.toLowerCase() === 'p') {
        appendSimilar(relative, similar[i]);
        break;
      }
    }

    range = Math.floor(post.children.length / 3) + e;
  }
})();

/**
 * Append similar contents to single template
 *
 * @since 1.5
 * @version 1.10
 */

(function() {
  var content = document.querySelector('.entry-content');


  /**
   * Check if similar contents exist
   */
  if(typeof knife_similar_contents === 'undefined' || content === null) {
    return false;
  }


  /**
   * Shuffle similar contents array
   */
  var similar = (function(items) {
    for(var i = 0, c; i < items.length; i++) {
        c = Math.floor(Math.random() * i);

        var temp = items[i];
        items[i] = items[c];
        items[c] = temp;
    }

    return items;
  }(knife_similar_contents.similar || []));


  /**
   * Create similar block
   */
  function appendSimilar(following) {
    var wrap = document.createElement('figure');
    wrap.classList.add('figure', 'figure--similar');

    var title = document.createElement('h4');
    title.innerHTML = knife_similar_contents.title || '';
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
   * Create auto similar contents if need
   */
  (function() {
    // Check if entry-content exists and it's long enough
    if(content.children.length < 10 || similar.length < 1) {
      return false;
    }

    // Find start point
    var landmark = Math.floor(content.children.length / 1.5);

    // Check if manual similar alread exists
    if(content.querySelector('.figure--similar') !== null) {
      return false;
    }

    var allowed = ['p', 'blockquote'];

    for(var i = landmark; i < content.children.length - 5; i++) {
      var relative = content.children[i];

      // Check if next tag in allowed list
      if(allowed.indexOf(relative.tagName.toLowerCase()) < 0) {
        continue;
      }

      if(typeof content.children[i - 1] === 'undefined') {
        continue;
      }

      var following = content.children[i - 1];

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
    if(typeof knife_similar_contents.action === 'undefined') {
      return false;
    }

    var figure = content.querySelectorAll('.figure--similar') || [];

    for(var i = 0; i < figure.length; i++) {
      var items = figure[i].querySelectorAll('a');

      // Set attributes for all similar links
      for(var e = 0; e < items.length; e++) {
        items[e].setAttribute('data-action', knife_similar_contents.action);

        if(items[e].href) {
          items[e].setAttribute('data-label', items[e].href);
        }
      }
    }
  })();
})();

/**
 * Append similar contents to single template
 *
 * @since 1.5
 * @version 1.11
 */


(function() {
  /**
   * Check if similar contents exist
   */
  if(typeof knife_similar_posts === 'undefined') {
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
  }(knife_similar_posts.similar || []));


  /**
   * Define array counter
   *
   * @since 1.11
   */
  var counter = 0;


  /**
   * Create similar link
   *
   * @since 1.11
   */
  function appendLink(wrap, similar) {
    var item = document.createElement('p');
    wrap.appendChild(item);

    var link = document.createElement('a');
    link.href = similar.link;
    link.innerHTML = similar.title;
    item.appendChild(link);
  }


  /**
   * Add footer similar links at first
   *
   * @since 1.11
   */
  (function() {
    var footer = document.querySelector('.entry-footer');

    // Skip if no footer or similar to short
    if(footer === null || similar.length < counter + 4) {
      return false;
    }

    var widget = document.createElement('div');
    widget.classList.add('entry-footer__similar');

    // Add similar items
    for(var i = counter; i < counter + 4; i++) {
      if(!similar[i].link || !similar[i].title) {
        continue;
      }

      // Create similar links
      appendLink(widget, similar[i]);
    }

    counter = counter + 4;

    // Append widget to footer
    footer.appendChild(widget);
  })();


  /**
   * Create auto similar contents if need
   */
  (function() {
    // Check if hidden is not true
    var content = document.querySelectorAll('.entry-content');

    // Skip cards and posts with hidden meta
    if(content.length !== 1 || knife_similar_posts.hidden == 1) {
      return false;
    }

    var children = content[0].children;

    // Check if entry-content long enough and similar links exist
    if(children.length < 15 || similar.length < counter + 3) {
      return false;
    }

    var allowed = ['p', 'blockquote'];

    // Find start point
    var landmark = Math.floor(children.length / 1.5);

    // Define following tag
    var following = null;

    // Find following tag
    for(var i = landmark; i < children.length - 5; i++) {
      // Check if next tag in allowed list
      if(allowed.indexOf(children[i].tagName.toLowerCase()) < 0) {
        continue;
      }

      if(typeof children[i - 1] === 'undefined') {
        continue;
      }

      // Check if prev tag in allowed list
      if(allowed.indexOf(children[i - 1].tagName.toLowerCase()) < 0) {
        continue;
      }

      following = children[i - 1];
      break;
    }

    // Let's insert similar posts if following found
    if(following === null) {
      return false;
    }

    var figure = document.createElement('figure');
    figure.classList.add('figure', 'figure--similar');

    var title = document.createElement('h4');
    title.innerHTML = knife_similar_posts.title || '';
    figure.appendChild(title);

    // Add similar items
    for(var i = counter; i < counter + 3; i++) {
      if(!similar[i].link || !similar[i].title) {
        continue;
      }

      // Create similar links
      appendLink(figure, similar[i]);
    }

    counter = counter + 3;

    // Insert block
    following.parentNode.insertBefore(figure, following.nextSibling);
  })();
})();

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
  if(typeof knife_similar_posts === 'undefined' || knife_similar_posts.length < 1) {
    return false;
  }


  /**
   * Check if entry-content exists and it's long enough
   */
  if(post === null || post.children.length < 5) {
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



  console.log(similar);

})();

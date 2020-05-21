/**
 * Custom comments perfomance checker
 *
 * @since 1.13
 */

(function() {
  if(typeof knife_meta_parameters === 'undefined') {
    return false;
  }

  if(typeof knife_meta_parameters.postid === 'undefined') {
    return false;
  }

  var post = knife_meta_parameters.postid;

  // Try to load comments
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '/id/comments?post=' + post, false);
  xhr.send();
})();
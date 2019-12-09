/**
 * Comments loader using Hypercomments api
 *
 * @since 1.4
 * @version 1.6
 */

(function() {
  var comments = document.getElementById('hypercomments_widget');


  /**
   * Check if hypercomments id and load button defined
   */
  if(comments === null || typeof knife_comments_id === 'undefined') {
    return false;
  }


  /**
   * Hypercomments options
   */
  _hcwp = window._hcwp || [];

  _hcwp.push({
    widget: "Stream",
    widget_id: knife_comments_id,
    callback: function() {
      comments.classList.add('comments--expand');
    }
  });


  /**
   * Widget first load
   */
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.async = true;
  script.src = 'https://w.hypercomments.com/widget/hc/' + knife_comments_id + '/ru/widget.js';
  document.head.appendChild(script);
})();

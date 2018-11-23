/**
 * Comments loader using Hypercomments api
 *
 * @since 1.4
 */

(function() {
  var button = document.getElementById('load-comments');


  /**
   * Check if hypercomments id and load button defined
   */
  if(button === null || typeof knife_comments_id === 'undefined') {
    return false;
  }


  /**
   * Declare global variables
   */
  var comments = null;


  /**
   * Widget first load
   */
  var loadWidget = function(callback) {
    _hcwp = window._hcwp || [];

    _hcwp.push({
      widget: "Stream",
      widget_id: knife_comments_id,
      callback: callback
    });

    // Create comments outer element
    var widget = document.createElement('div');
    widget.classList.add('entry-comments__widget');
    button.parentNode.appendChild(widget);

    // Create comments widget with hypercomments id
    comments = document.createElement('div');
    comments.id = 'hypercomments_widget';
    comments.classList.add('comments');
    widget.appendChild(comments);

    // Append js script
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.async = true;
    script.src = 'https://w.hypercomments.com/widget/hc/' + knife_comments_id + '/ru/widget.js';
    document.head.appendChild(script);
  }


  /**
   * Toggle comments on load button click
   */
  button.addEventListener('click', function(e) {
    e.preventDefault();

    // If comments not loaded yet
    if(comments === null) {
      var label = button.textContent;
      button.innerHTML = '<span class="icon icon--loop"></span>';

      return loadWidget(function() {
        button.innerHTML = label;
        comments.classList.add('comments--expand');
      });
    }

    return comments.classList.toggle('comments--expand');
  });
})();

/**
 * Comments loader using Hypercomments api
 *
 * @since 1.4
 */

(function() {
  var button = document.getElementById('load-comments');

  // Check if hypercomments id and load button defined
  if(button === null || typeof knife_comments_id === 'undefined') {
    return false;
  }

  // Declare global variables
  var widget = null;

  // Widget first load
  var loadWidget = function(callback) {
    _hcwp = window._hcwp || [];

    _hcwp.push({
      widget: "Stream",
      widget_id: knife_comments_id,
      callback: callback
    });

    widget = document.createElement('div');
    widget.classList.add('entry-footer__comments');
    widget.id = 'hypercomments_widget';

    var footer = button.nextElementSibling;
    if(footer !== null) {
      footer.insertBefore(widget, footer.firstChild);
    }

    // Append js script
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.async = true;
    script.src = "https://w.hypercomments.com/widget/hc/" + knife_comments_id + "/ru/widget.js";

    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(script, s.nextSibling);
  }


  // Toggle comments on load button click
  button.addEventListener('click', function(e) {
    e.preventDefault();

    // If widget not loaded yet
    if(widget === null) {
      var label = button.textContent;
      button.innerHTML = '<span class="icon icon--loop"></span>';

      return loadWidget(function() {
        button.innerHTML = label;
        widget.classList.add('comments');
      });
    }

    return widget.classList.toggle('comments');
  });
})();

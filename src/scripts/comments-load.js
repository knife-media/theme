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


  _hcwp = window._hcwp || [];

  _hcwp.push({
    widget: "Stream",
    widget_id: knife_comments_id
  });


  // Add comments widget to entry-footer
  var widget = document.createElement('div');
  widget.classList.add('entry-footer__comments', 'comments');
  widget.id = 'hypercomments_widget';
  var i = document.querySelector('.entry-footer__inner');
  i.insertBefore(widget, i.firstChild);


  // Append js script
  var script = document.createElement("script");
  script.type = "text/javascript";
  script.async = true;
  script.src = "https://w.hypercomments.com/widget/hc/" + knife_comments_id + "/ru/widget.js";

  var s = document.getElementsByTagName("script")[0];
  s.parentNode.insertBefore(script, s.nextSibling);


  // Toggle comments on load button click
  button.addEventListener('click', function(e) {
    e.preventDefault();

    widget.classList.toggle('comments--expand');
  });
})();

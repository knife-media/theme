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


  // Show comments counter on load button
  var commentsCounter = function() {
    _hcwp = window._hcwp || [];

    var title = "<strong>{%COUNT%}</strong>";

    _hcwp.push({
      widget: "Bloggerstream",
      widget_id: knife_comments_id,
      selector: "#load-comments",
      label: button.textContent + title
    });

    var script = document.createElement("script");
    script.type = "text/javascript";
    script.async = true;
    script.src = "https://w.hypercomments.com/widget/hc/" + knife_comments_id + "/ru/widget.js";

    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(script, s.nextSibling);
  }




  return commentsCounter();
var x = document.createElement('div');

  document.querySelector('.entry-footer__board > button').addEventListener('click', function(e) {
    e.preventDefault();

    x.id = 'hypercomments_widget';
    x.classList.add('comments');
    var i = document.querySelector('.entry-footer__inner');
    i.insertBefore(x, i.firstChild);

    initHyperComments(103542);

  });


  var initHyperComments = function(id) {
    if('HC_LOAD_INIT' in window) {
      return;
    }

    _hcwp = window._hcwp || [];
    _hcwp.push({
      widget: "Stream",
      widget_id: id,
      callback: function(app, init){
      }
    });

    window.HC_LOAD_INIT = true;

    var lang = 'ru';
    var hcc = document.createElement("script");
    hcc.type = "text/javascript";
    hcc.async = true;
    hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://w.hypercomments.com/widget/hc/103542/"+lang+"/widget.js";

    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hcc, s.nextSibling);
  }



})();

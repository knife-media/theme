/**
 * Append Relap links to entry footer
 *
 * @since 1.5
 */

(function() {
  if(typeof knife_relap_options === 'undefined') {
    return false;
  }

  var options = knife_relap_options;

  if(options.token.length > 1) {
    var script = document.createElement("script");
    script.type = 'text/javascript';
    script.async = true;
    script.src = 'https://relap.io/api/v6/head.js?token=' + options.token;
    document.head.appendChild(script);
  }

  if(options.block.length > 1) {
    widget = document.createElement('div');
    widget.classList.add('entry-footer__relap');
    widget.innerHTML = '<script id="' + options.block + '"></script>';
    document.querySelector('.entry-footer__inner').appendChild(widget);
  }
})();

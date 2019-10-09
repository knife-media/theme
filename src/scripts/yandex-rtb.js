/**
 * Append yandex rtb blocks
 *
 * @since 1.5
 * @version 1.10
 */

(function() {
  window['yandexContextAsyncCallbacks'] = window['yandexContextAsyncCallbacks'] || [];

  // Create rtb on entry-footer if exists
  (function() {
    if(typeof knife_yandex_rtb === 'undefined') {
      return false;
    }

    var footer = document.querySelector('.entry-footer');

    if(footer === null) {
      return false;
    }

    // Create rtb widget element
    var widget = document.createElement('div');
    widget.classList.add('entry-footer__rtb', 'widget-rtb');
    widget.id = 'yandex_rtb_' + knife_yandex_rtb;
    footer.appendChild(widget);

    // Add rtb to render queue
    window['yandexContextAsyncCallbacks'].push(function() {
      Ya.Context.AdvManager.render({
        blockId: knife_yandex_rtb,
        renderTo: widget.id,
        async: true,
        onRender: function(data) {
          widget.classList.add('widget-rtb--rendered');
        }
      });
    });
  })();


  // Append Yandex RTB handler script
  if(window['yandexContextAsyncCallbacks'].length < 1) {
    return false;
  }

  // Append js script
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.async = true;
  script.src = 'https://an.yandex.ru/system/context.js';
  document.head.appendChild(script);
})();

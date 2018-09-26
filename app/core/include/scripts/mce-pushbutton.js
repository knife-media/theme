(function() {
  tinymce.PluginManager.add('pushbutton', function(editor, url) {
    editor.addButton('pushbutton', {
      image: 'data:image/svg+xml;base64,PHN2ZyB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2ZXJzaW9uPSIxLjEiIHg9IjAiIHk9IjAiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiIHZpZXdCb3g9Ii0zIC0zIDIwIDIwIj48cGF0aCB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9IiM1NTVkNjYiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTIsMTIgTDIsMiBMNSwyIEw1LDAgTDIsMCBDMC44OSwwIDAsMC45IDAsMiBMMCwxMiBDMCwxMy4xIDAuODksMTQgMiwxNCBMMTIsMTQgQzEzLjEsMTQgMTQsMTMuMSAxNCwxMiBMMTQsOSBMMTIsOSBMMTIsMTIgTDIsMTIgWiBNNywyIEwxMC41OSwyIEw0Ljc2LDcuODMgTDYuMTcsOS4yNCBMMTIsMy40MSBMMTIsNyBMMTQsNyBMMTQsMCBMNywwIEw3LDIgWiIvPjwvc3ZnPg==',
      title: 'Вставить кнопку',
      onclick: function() {
        editor.windowManager.open( {
          title: 'Вставить кнопку',
          width: 400,
          height: 110,
          body: [
            {
              type: 'textbox',
              name: 'href',
              label: 'Ссылка'
            },
            {
              type: 'textbox',
              name: 'name',
              label: 'Текст ссылки'
            },
          ],
          onsubmit: function(e) {
            editor.insertContent( '<a class="button" href="' + e.data.href + '" target="_blank" rel="noopener">' + e.data.name  + '</a>');
          }
        });
      }
    });
  });
})();

/**
 * Add reference block
 *
 * @since 1.17
 */

(function () {
  tinymce.PluginManager.add('preview-image', function (editor, url) {
    editor.addButton('preview-image', {
      image: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAllBMVEVHcEwAAAAAAAAAAABVVVVJSUlAQEA5OTkzMzMuLi5VVVVOTk5EREQ8PDw5OTlNTU1GRkZAQEBERERKSkpGRkZGRkZFRUVERERHR0dISEhHR0dGRkZGRkZGRkZHR0dGRkZJSUlISEhISEhHR0dERERERERDQ0NDQ0NHR0dERERDQ0NDQ0NHR0dHR0dGRkZFRUVHR0dGRkZyEGCvAAAAMXRSTlMAAQIDBgcICQoLDA0PERIUFhgeHyFFRkdleXp7g4SNlpeYmeDh4uPr7vHy8/T19vn8lvbPiQAAANxJREFUeNrt0lV6xTAQQ2FdZmZmJmv/m+sXmlISTxn/9xOQDb1/7eXWMIbZLtp4oHei1bEL0T5R4diSYEmVuQRbqqwlcP93nEGkzNj9cwToqCJGhY7HQREx8uHBmaGuPynQ//SrZp1kYw5uEhIovH9w2fuuuqCfgC81CAke3db0kLwlIcohQQMPlcgLgIIjD+Rts6ZHbpCPOWkhfloQMqsT5C6OnROoZhW6WW8piKrqagxrFV99+jgwVDESbKiykmBJlZkE7SMVDk2IrqI4dPBAa742jGHWsybU/t0BqDLFPub2aoAAAAAASUVORK5CYII=',
      title: 'Вставить превью',
      onclick: function () {
        editor.windowManager.open({
          title: 'Вставить превью',
          width: 400,
          height: 80,
          body: [
            {
              type: 'textbox',
              name: 'href',
              label: 'Ссылка на изображение'
            },
          ],
          onsubmit: function (e) {
            var link = e.data.href;
            link = link.replace('https://knife.media/wp-content/uploads/', 'https://knife.support/images/');

            var selection = editor.selection.getContent({
              'format': 'text'
            });

            if (e.data.href.length > 0) {
              selection = '<a href="' + link + '" target="_blank" rel="noopener" data-preview>' + selection + '</a>';
            }

            editor.execCommand('mceReplaceContent', false, selection);
          }
        });
      }
    });
  });
})();
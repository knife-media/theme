/**
 * Add script embed button
 *
 * @since 1.11
 */
(function () {
  var entities = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  tinymce.PluginManager.add('script-embed', function (editor, url) {
    editor.addButton('script-embed', {
      image: 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDUxMiA1MTIiIGZpbGw9IiM1NTVkNjYiPjxwYXRoIGQ9Ik0yODggMzY4bDQ4IDQ4IDE2MC0xNjAtMTYwLTE2MC00OCA0OCAxMTIgMTEyeiI+PC9wYXRoPjxwYXRoIGQ9Ik0yMjQgMTQ0bC00OC00OC0xNjAgMTYwIDE2MCAxNjAgNDgtNDgtMTEyLTExMnoiPjwvcGF0aD48L3N2Zz4=',
      title: 'Добавить внешний скрипт',
      onclick: function () {
        var node = editor.selection.getNode();

        editor.windowManager.open({
          title: 'Вставить внешний скрипт',
          width: 400,
          height: 200,
          body: [{
            type: 'textbox',
            multiline: true,
            name: 'text',
            minHeight: 170,
            value: node.dataset.body || ''
          }, ],

          onsubmit: function (e) {
            var script = e.data.text;

            if (script.length < 1) {
              return;
            }

            // Replace entities and new lines
            script = script.replace(/\n|\r/g, '').replace(/\s{2,}/g, ' ');

            script = script.replace(/[&<>"']/g, function (c) {
              return entities[c];
            });

            html = '<figure class="figure figure--script" data-script="' + script + '"></figure>';

            editor.insertContent(html);
          }
        });
      }
    });

    // Replace card comment with hr
    editor.on('BeforeSetContent', function (event) {
      var regex = /<figure[^>]+data-script="(.+?)".*?><\/figure>/g;

      event.content = event.content.replace(regex, function (match, script) {
        var image = '<img ' + 'src="' + tinymce.Env.transparentSrc + '" ' +
          'data-wp-script="' + encodeURIComponent(script) + '" ' +
          'data-mce-resize="false" data-mce-placeholder="1">';

        return image;
      });
    });

    // Replace script
    editor.on('PostProcess', function (event) {
      if (!event.get) {
        return;
      }

      event.content = event.content.replace(/<img[^>]+>/g, function (image) {
        var html;

        if (match = image.match(/ data-wp-script="([^"]+)"/)) {
          html = '<figure class="figure figure--script" data-script="' + decodeURIComponent(match[1]) + '"></figure>';
        }

        return html || image;
      });
    });
  });
})();
/**
 * Add reference block
 *
 * @since 1.10
 */
(function() {
  var entities = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  tinymce.PluginManager.add('reference-block', function(editor, url) {
    editor.addButton('reference-block', {
      image: 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDUxMiA1MTIiIGZpbGw9IiM1NTVkNjYiPjxwYXRoIGQ9Ik0yNjggNjRsLTM2IDM2IDM2IDM2LTg0IDk2aC04NGw2NiA2Ni0xMDIgMTM1LjIzMXYxNC43NjloMTQuNzY5bDEzNS4yMzEtMTAyIDY2IDY2di04NGw5Ni04NCAzNiAzNiAzNi0zNi0xODAtMTgwek0yMzIgMjY4bC0yNC0yNCA4NC04NCAyNCAyNC04NCA4NHoiPjwvcGF0aD48L3N2Zz4=',
      title: 'Добавить аннотацию',
      onclick: function() {
        var node = editor.selection.getNode();
        var $ = jQuery;

        editor.windowManager.open({
          title: 'Вставить аннотацию',
          width: 600,
          height: 180,
          body: [
            {
              type: 'textbox',
              multiline: true,
              name: 'text',
              minHeight: 150,
              value: node.dataset.body || ''
            },
          ],

          onsubmit: function(e) {
            var selection = editor.selection.getContent({'format': 'text'});

            if($(node).is('span[data-body]')) {
              selection = $(node).text();

              //  remove node after get it text
              $(node).remove();
            }

            if(e.data.text.length > 0) {
              // Replace entities
              var body = e.data.text.replace(/[&<>"']/g, function(c) {
                return entities[c];
              });

              selection = '<span data-body="' + body  + '">' + selection + '</span>';
            }

            editor.execCommand('mceReplaceContent', false, selection);
          }
        });
      }
    });
  });
})();

/**
 * Add reference block
 *
 * @since 1.10
 */
(function() {
  tinymce.PluginManager.add('reference-block', function(editor, url) {
    editor.addButton('reference-block', {
      image: 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDUxMiA1MTIiIGZpbGw9IiM1NTVkNjYiPjxwYXRoIGQ9Ik0yNjggNjRsLTM2IDM2IDM2IDM2LTg0IDk2aC04NGw2NiA2Ni0xMDIgMTM1LjIzMXYxNC43NjloMTQuNzY5bDEzNS4yMzEtMTAyIDY2IDY2di04NGw5Ni04NCAzNiAzNiAzNi0zNi0xODAtMTgwek0yMzIgMjY4bC0yNC0yNCA4NC04NCAyNCAyNC04NCA4NHoiPjwvcGF0aD48L3N2Zz4=',
      title: 'Добавить аннотацию',
      onclick: function() {
        editor.windowManager.open({
          title: 'Вставить аннотацию',
          width: 600,
          height: 250,
          body: [
            {
              type: 'textbox',
              name: 'title',
              label: 'Заголовок'
            },

            {
              type: 'textbox',
              multiline: true,
              name: 'text',
              label: 'Текст аннотации',
              minHeight: 180
            },
          ],
          onsubmit: function(e) {
            editor.insertContent('<figure class="figure figure--similar"><h4>' + e.data.title  + '</h4></figure>');
          }
        });
      }
    });
  });
})();

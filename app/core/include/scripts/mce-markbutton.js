(function() {
  tinymce.PluginManager.add('markbutton', function(editor, url) {
    editor.on('init', function() {
        editor.formatter.register('markbutton', {
            inline: 'mark'
        });
    });

    editor.addButton('markbutton', {
      text: 'Выделить',
      onclick: function() {
        tinymce.activeEditor.formatter.toggle('markbutton');
      }
    });
  });
})();

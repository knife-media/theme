(function() {
  tinymce.PluginManager.add('markbutton', function(editor, url) {
    editor.on('init', function() {
        editor.formatter.register('markbutton', {
            inline: 'mark'
        });
    });

    editor.addButton('markbutton', {
      image: 'data:image/svg+xml;base64,PHN2ZyB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2ZXJzaW9uPSIxLjEiIHg9IjAiIHk9IjAiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiIHZpZXdCb3g9Ii0zIC0zIDIwIDIwIj48cGF0aCBmaWxsPSIjNTU1ZDY2IiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik02LDUgTDIsOSBMMywxMCBMMCwxMyBMNCwxMyBMNSwxMiBMNSwxMiBMNiwxMyBMMTAsOSBMNiw1IEw2LDUgWiBNMTAuMjkzNzg1MSwwLjcwNjIxNDkwNSBDMTAuNjgzODE2OCwwLjMxNjE4MzE4MyAxMS4zMTM4NzMzLDAuMzEzODczMjkxIDExLjcwNTkxMjEsMC43MDU5MTIwNTQgTDE0LjI5NDA4NzksMy4yOTQwODc5NSBDMTQuNjgzOTUyNCwzLjY4Mzk1MjQxIDE0LjY3OTY4NTIsNC4zMjAzMTQ3NiAxNC4yOTM3ODUxLDQuNzA2MjE0OSBMMTEsOCBMNyw0IEwxMC4yOTM3ODUxLDAuNzA2MjE0OTA1IFoiLz48L3N2Zz4=',
      title: 'Выделить текст',
      onclick: function() {
        tinymce.activeEditor.formatter.toggle('markbutton');
      }
    });
  });
})();

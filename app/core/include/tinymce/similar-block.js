(function() {
  tinymce.PluginManager.add('similar-block', function(editor, url) {
    editor.addButton('similar-block', {
      image: 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjU0NCIgaGVpZ2h0PSI1MTIiIHZpZXdCb3g9IjAgMCA1NDQgNTEyIiBmaWxsPSIjNTU1ZDY2Ij48cGF0aCBkPSJNMjk3LjE0MyA2NGMxMDYuMDM5IDAgMTkyIDg1Ljk2MSAxOTIgMTkycy04NS45NjEgMTkyLTE5MiAxOTJ2LTQxLjE0M2M0MC4yOTYgMCA3OC4xNzktMTUuNjkyIDEwNi42NzItNDQuMTg1czQ0LjE4NS02Ni4zNzYgNDQuMTg1LTEwNi42NzJjMC00MC4yOTUtMTUuNjkyLTc4LjE3OS00NC4xODUtMTA2LjY3MnMtNjYuMzc2LTQ0LjE4NS0xMDYuNjcyLTQ0LjE4NWMtNDAuMjk1IDAtNzguMTc5IDE1LjY5Mi0xMDYuNjcyIDQ0LjE4NS0yMS45MTcgMjEuOTE2LTM2LjI0OSA0OS4zOTEtNDEuNzAzIDc5LjI0NGg3OS44MDNsLTk2IDEwOS43MTQtOTYtMTA5LjcxNGg3MC41M2MxMy4zMTEtOTMuMDQ0IDkzLjMxNi0xNjQuNTcxIDE5MC4wNDEtMTY0LjU3MXpNMzc5LjQyOSAyMjguNTcxdjU0Ljg1N2gtMTA5LjcxNHYtMTM3LjE0M2g1NC44NTd2ODIuMjg2eiI+PC9wYXRoPjwvc3ZnPg==',
      title: 'Добавить блок похожих записей',
      onclick: function() {
        editor.windowManager.open({
          title: 'Вставить блок похожих записей',
          width: 400,
          height: 80,
          body: [
            {
                type: 'textbox',
                name: 'title',
                label: 'Заголовок блока'
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

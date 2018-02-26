(function() {
	tinymce.PluginManager.add('push', function(editor, url) {
		editor.addButton('push', {
			text: 'Кнопка',
			icon: false,
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

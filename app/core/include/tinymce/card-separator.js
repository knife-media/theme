/**
 * Tinymce card separator styles
 *
 * @since 1.10
 */
(function () {
  tinymce.PluginManager.add('card-separator', function (editor, url) {

    editor.addButton('card-separator', {
      icon: 'wp_more',
      title: 'Вставить разрыв',
      onclick: function () {
        editor.insertContent('<!--card-->');
      }
    });

    // Replace card comment with hr
    editor.on('BeforeSetContent', function (event) {
      event.content = event.content.replace(/<!--card-->/g,
        '<img src="' + tinymce.Env.transparentSrc + '" data-wp-more="card" class="wp-more-tag" ' +
        'alt="" data-mce-resize="false" data-mce-placeholder="1" />'
      );
    });

    // Replace hr tag with comment
    editor.on('PostProcess', function (event) {
      if (!event.get) {
        return;
      }

      event.content = event.content.replace(/<img[^>]+>/g, function (image) {
        var html;

        if (image.indexOf('data-wp-more="card"') !== -1) {
          html = '<!--card-->';
        }

        return html || image;
      });
    });
  });
})();
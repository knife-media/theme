/**
 * Tinymce custom figure handler
 *
 * @since 1.9
 */
(function() {
    tinymce.PluginManager.add('figure-helper', function(editor, url) {
      // Trigger on editor change
      editor.on('NodeChange', function (e) {
        // Check if manipulate with figure
        if (e.element.nodeName.toLowerCase() === 'figure') {
          var content = jQuery(editor.contentDocument);

          // Remove all empty figures
          content.find('figure').each(function(i, item) {
            var figure = jQuery(item);

            if (figure.children(':not(br)').length < 1) {
              figure.remove();
            }
          });
        }
      });

      // Trigger before set content
      editor.on('BeforeSetContent', function (e) {
        var node = editor.selection.getNode();

        // Move cursor to the end of figure block
        if (node.nodeName.toLowerCase() === 'figure') {
          editor.selection.select(node);
          editor.selection.collapse(false);
        }
      });
    });
})();


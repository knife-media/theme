(function() {
  tinymce.PluginManager.add('quote-button', function(editor, url) {
    editor.addButton('quote-button', {
      icon: 'blockquote',
      title: 'Добавить цитату',
      onclick: function() {
        var fromElement = $(editor.selection.getStart());
        var lastElement = $(editor.selection.getEnd());

        if(lastElement.is(fromElement.prev())) {
          lastElement = fromElement;
        }

        if(!fromElement.parent().is('body')) {
          fromElement = fromElement.parentsUntil('body').last();
        }

        if(!lastElement.parent().is('body')) {
          lastElement = lastElement.parentsUntil('body').last();
        }

        var currentElement = fromElement;
        var rangeElements = $();
        var rootElement = $(editor.getBody());

        var hasBlockquote = false;

        for(var i = 0; i < rootElement.children().length; i++) {
          var nextElement = currentElement.next();

          if(currentElement.closest('figure.figure--quote').length > 0) {
            currentElement.replaceWith(currentElement.find('blockquote').html());

            hasBlockquote = true;
          }

          currentElement.attr('is-quote', '');

          if(currentElement.is(lastElement)) {
            break;
          }

          currentElement = nextElement;
        }

        if(hasBlockquote === false) {
          var wrapper = '<figure class="figure figure--quote"><blockquote></blockquote></figure>';

          rootElement.find('[is-quote]').wrapAll(wrapper);
        }

        rootElement.find('[is-quote]').removeAttr('is-quote');
      }
    });
  });
})();

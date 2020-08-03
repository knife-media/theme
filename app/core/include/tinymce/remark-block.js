(function () {
  tinymce.PluginManager.add('remark-block', function (editor, url) {
    editor.addButton('remark-block', {
      image: 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDUxMiA1MTIiIGZpbGw9IiM1NTUiPgo8cGF0aCBkPSJNNDYxLjUyOSAyMDkuODU2bC0xNDIuMDE3LTIwLjYzNy02My41MTMtMTI4LjY5LTYzLjUxMyAxMjguNjktMTQyLjAxOCAyMC42MzcgMTAyLjc2NiAxMDAuMTcxLTI0LjI2IDE0MS40NDQgMTI3LjAyNC02Ni43ODEgMTI3LjAyNCA2Ni43ODEtMjQuMjYtMTQxLjQ0MyAxMDIuNzY2LTEwMC4xNzF6Ij48L3BhdGg+Cjwvc3ZnPgo=',
      title: 'Добавить замечание',
      onclick: function () {
        var $ = jQuery;

        var fromElement = $(editor.selection.getStart());
        var lastElement = $(editor.selection.getEnd());

        if (lastElement.is(fromElement.prev())) {
          lastElement = fromElement;
        }

        if (!fromElement.parent().is('body')) {
          fromElement = fromElement.parentsUntil('body').last();
        }

        if (!lastElement.parent().is('body')) {
          lastElement = lastElement.parentsUntil('body').last();
        }

        var currentElement = fromElement;
        var rangeElements = $();
        var rootElement = $(editor.getBody());

        var hasRemark = false;

        for (var i = 0; i < rootElement.children().length; i++) {
          var nextElement = currentElement.next();

          if (currentElement.closest('figure.figure--remark').length > 0) {
            currentElement.replaceWith(currentElement.find('blockquote').html());

            hasRemark = true;
          }

          currentElement.attr('is-remark', '');

          if (currentElement.is(lastElement)) {
            break;
          }

          currentElement = nextElement;
        }

        if (hasRemark === false) {
          var wrapper = '<figure class="figure figure--remark"><blockquote></blockquote></figure>';

          rootElement.find('[is-remark]').wrapAll(wrapper);
        }

        rootElement.find('[is-remark]').removeAttr('is-remark');
      }
    });
  });
})();

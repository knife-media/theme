jQuery(document).ready(function($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-quiz-box');

  // Use this variable as storage for current editor id
  box.data('items', 0);


  // Sort items
  box.sortable({
    items: '.item',
    handle: '.dashicons-menu',
    axis: 'y'
  }).disableSelection();


  // Create wp.editor using item element
  var editor = function(el) {
    var text = el.find('.option__general-question');
    var edit = text.attr('id');

    if(typeof edit === 'undefined') {
      edit = 'knife-quiz-text-' + box.data('items');
      text.attr('id', edit);

      box.data().items++;
    } else {
      wp.editor.remove(edit);
    }

    wp.editor.initialize(edit, {
      tinymce: true,
      quicktags: true,
      mediaButtons: true,
      tinymce: {
        toolbar1: 'bold,italic,bullist,numlist,link'
      }
    });
  }

  // Reinit wp editor on drag
  box.on('sortstop', function(event, ui) {
    return editor(ui.item);
  });


  // Init wp editors
  box.find('.item').each(function(i, el) {
    return editor($(this));
  });
});

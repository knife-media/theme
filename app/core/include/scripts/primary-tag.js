jQuery(document).ready(function($) {
  var box = $('#tagsdiv-post_tag');

  if(box.length < 1)
    return false;

  var input = $('<input>', {
    name: 'primary-tag',
    type: 'hidden'
  });

  box.append(input);

  var howto = $('<p>', {
    class: 'howto',
    html: knife_primary_tag.howto
  });

  box.find('.tagchecklist').before(howto);

  var getTerm = function(el) {
    var term = el.clone().children().remove().end();

    return term.text().trim();
  }

  box.on('click', '.tagchecklist > li', function(e) {
    var el = $(this);

    // add selected term to input
    input.val(getTerm(el));

    // remove selected class from other items
    box.find('.tagchecklist > li').removeClass('selected');

    return el.addClass('selected');
  });

  box.on('click', '.tagchecklist > li.selected', function(e) {
    var el = $(this);

    // remove term from input
    input.val('');

    return el.removeClass('selected');
  });
});

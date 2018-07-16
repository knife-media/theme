jQuery(document).ready(function($) {
  var box = $("#knife-selection-box");

  // sort items
  box.sortable({
    containment: 'parent',
    items: '.knife-selection-item',
    handle: '.dashicons-menu',
    placeholder: 'knife-selection-dump'
  }).disableSelection();


  // add new item
  box.on('click', '#knife-selection-add', function(e) {
    e.preventDefault();

    var link = box.find('#knife-selection-link').val();
    var text = box.find('#knife-selection-text').val();

    if(link.length < 1 || text.length < 1)
      return;

    var item = box.find('.knife-selection-item:first').clone();

    item.find('.item-text > h1').html(text);
    item.find('.item-text > input').val(text);

    item.find('.item-link > a').html(link);
    item.find('.item-link > a').attr('href', link);
    item.find('.item-link > input').val(link);

    box.find('#knife-selection-link').val('');
    box.find('#knife-selection-text').val('');
    box.find('.knife-selection-items').append(item);

    return item.removeClass('hidden');
  });

  box.on('click', '.dashicons-trash', function(e) {
    var item = $(this).closest('.knife-selection-item');

    return item.remove();
  });
});

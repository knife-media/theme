jQuery(document).ready(function($) {
  var box = $("#knife-select-box");

  // Sort items
  box.sortable({
    containment: 'parent',
    items: '.knife-select-item',
    handle: '.dashicons-menu',
    placeholder: 'knife-select-dump'
  }).disableSelection();


  // Add new item
  box.on('click', '#knife-select-add', function(e) {
    e.preventDefault();

    var link = box.find('#knife-select-link').val();
    var text = box.find('#knife-select-text').val();

    if(link.length < 1) {
      return;
    }

    var item = box.find('.knife-select-item:first').clone();

    item.find('.item-text > h1').html(text);
    item.find('.item-text > input').val(text);

    item.find('.item-link > a').html(link);
    item.find('.item-link > a').attr('href', link);
    item.find('.item-link > input').val(link);

    box.find('#knife-select-link').val('');
    box.find('#knife-select-text').val('');
    box.find('.knife-select-items').append(item);

    return item.removeClass('hidden');
  });


  box.on('click', '.dashicons-trash', function(e) {
    var item = $(this).closest('.knife-select-item');

    return item.remove();
  });
});

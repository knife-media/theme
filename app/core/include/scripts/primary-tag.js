jQuery(document).ready(function($) {
  var box = $('#tagsdiv-post_tag');

  var selected = knife_primary_tag.primary || '';
  var delimiter = (window.tagsSuggestL10n && window.tagsSuggestL10n.tagDelimiter) || ',';

  var checklist = box.find('.tagchecklist');

  if(box.length < 1 || typeof knife_primary_tag === 'undefined')
    return false;


  var parseTags = function() {
    var thetags = box.find('.the-tags').val();

    if($('#knife-primary-tag').length)
      $('#knife-primary-tag').remove();

    if(thetags.length < 1)
      return false;

    var block = $('<div>', {'id': 'knife-primary-tag'});

    $('<p>', {
      'class': 'howto',
      'html': knife_primary_tag.howto
    }).appendTo(block);

    $('<select>', {
      'name': 'primary-tag',
      'style': 'width: 98%'
    }).appendTo(block);

    block.appendTo(box.find('.tagsdiv'));


    $.each(thetags.split(delimiter), function(key, val) {
      val = $.trim(val);

      var obj = {'value': val, 'html': val};

      if(val === selected)
        obj.selected = selected;

      $('<option>', obj).appendTo(block.find('select'));
    });
  }

  var observer = window.MutationObserver || window.WebKitMutationObserver;

  view = new observer(function(mutations) {
    return parseTags();
  });

  view.observe(checklist[0], {
    subtree: true,
    attributes: false,
    childList: true,
    characterData: false
  });

  return parseTags();
});

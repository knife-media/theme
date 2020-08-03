function (plugin, args) {
  var whitelist = 'p,span,strong,em,h1,h2,h3,h4,h5,h6,ul,li,ol,a,b,i';
  var stripped = jQuery('<div>' + args.content + '</div>');
  var elements = stripped.find('*').not(whitelist);

  for (var i = elements.length - 1; i >= 0; i--) {
    var el = elements[i];

    jQuery(el).replaceWith(el.innerHTML);
  }

  stripped.find('*').removeAttr('id').removeAttr('class');
  args.content = stripped.html();
}
/**
 * Collapse and expand post cents widget
 *
 * @since 1.12
 */

(function() {
  var widgets = document.querySelectorAll('.widget-cents__inner');

  if(widgets.length === 0) {
    return false;
  }

  for(var i = 0; i < widgets.length; i++) {
    var content = widgets[i].querySelector('.widget-cents__content');

    // Create new content block
    var folded = document.createElement('div');
    folded.classList.add('widget-cents__folded');
    widgets[i].insertBefore(folded, content);

    // Get text from first paragraph
    var text = content.querySelector('p').textContent;

    // Create p with updated text
    var part = document.createElement('p');
    part.textContent = text.substring(0, 200).trim() + '… ';
    folded.appendChild(part);

    // Create strong button text
    var link = document.createElement('strong');
    link.textContent = content.getAttribute('data-folded');
    part.appendChild(link);

    // Show original content on click
    folded.addEventListener('click', function(e) {
      this.parentNode.removeChild(this);
    });
  }
})();

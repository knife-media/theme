/**
 * Typo reporter
 *
 * @since 1.12
 */

(function() {
  // Check mistype options existing
  if(typeof knife_mistype_reporter === 'undefined') {
    return false;
  }


  // Get option from global settings
  function getOption(option, alternate) {
    if(knife_mistype_reporter.hasOwnProperty(option)) {
      return knife_mistype_reporter[option];
    }

    return alternate || '';
  }


  /**
   * Show reporter popup
   */
  function showPopup(selection) {
    var mistype = document.createElement('div');
    mistype.classList.add('mistype');

    // Create popup modal
    var popup = document.createElement('div');
    popup.classList.add('mistype__popup');
    mistype.appendChild(popup);

    // Add popup title
    var heading = document.createElement('h3');
    heading.classList.add('mistype__popup-heading');
    heading.textContent = getOption('heading');
    popup.appendChild(heading);

    // Add selection
    var marked = document.createElement('p');
    marked.classList.add('mistype__popup-marked');
    marked.textContent = selection;
    popup.appendChild(marked);

    // Add textarea for comment
    var comment = document.createElement('textarea');
    comment.classList.add('mistype__popup-comment');
    comment.setAttribute('placeholder', getOption('textarea'));
    popup.appendChild(comment);

    // Add send button
    var submit = document.createElement('button');
    submit.classList.add('mistype__popup-submit', 'button');
    submit.textContent = getOption('button', 'Send');
    popup.appendChild(submit);

    document.body.appendChild(mistype);
  }


  /**
   * Event listener on keydown
   */
  document.addEventListener('keydown', function(e) {
    if(event.ctrlKey && event.keyCode == 13) {
      var selection = window.getSelection().toString();

      // If selection not empty
      if(selection.length > 0) {
        showPopup(selection);
      }
    }
  });
})();

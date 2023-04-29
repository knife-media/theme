/**
 * Typo reporter
 *
 * @since 1.12
 */

(function () {
  // Check mistype options existing
  if (typeof knife_mistype_reporter === 'undefined') {
    return false;
  }

  // Get option from global settings
  function getOption(option, alternate) {
    if (knife_mistype_reporter.hasOwnProperty(option)) {
      return knife_mistype_reporter[option];
    }

    return alternate || '';
  }


  /**
   * Send ajax request
   */
  function sendRequest(selection, comment, context) {
    let data = {
      'nonce': getOption('nonce'),
      'time': getOption('time'),
      'comment': comment,
      'context': context,
      'marked': selection,
      'location': document.location.href
    };

    // Send request
    const request = new XMLHttpRequest();
    request.open('POST', getOption('ajaxurl') + '/mistype');
    request.setRequestHeader('Content-Type', 'application/json');
    request.send(JSON.stringify(data));
  }


  /**
   * Show reporter popup
   */
  function showPopup(selection, context) {
    let mistype = document.querySelector('.mistype');

    if (mistype !== null) {
      mistype.parentNode.removeChild(mistype);
    }

    mistype = document.createElement('div');
    mistype.classList.add('mistype');
    document.body.appendChild(mistype);

    // Create popup modal
    const popup = document.createElement('div');
    popup.classList.add('mistype__popup');
    mistype.appendChild(popup);

    // Add popup title
    const heading = document.createElement('h3');
    heading.classList.add('mistype__popup-heading');
    heading.textContent = getOption('heading');
    popup.appendChild(heading);

    // Add selection
    const marked = document.createElement('p');
    marked.classList.add('mistype__popup-marked');
    marked.textContent = selection;
    popup.appendChild(marked);

    // Add textarea for comment
    const comment = document.createElement('textarea');
    comment.classList.add('mistype__popup-comment');
    comment.setAttribute('placeholder', getOption('textarea'));
    comment.setAttribute('maxlength', 300);
    popup.appendChild(comment);

    // Add send button
    const submit = document.createElement('button');
    submit.classList.add('mistype__popup-submit', 'button');
    submit.textContent = getOption('button', 'Send');
    popup.appendChild(submit);

    submit.addEventListener('click', function (e) {
      e.preventDefault();

      // Send AJAX request
      sendRequest(selection, comment.value, context);

      // Remove mistype popup
      mistype.parentNode.removeChild(mistype);
    });

    // Add close button
    const close = document.createElement('button');
    close.classList.add('mistype__popup-close');

    close.addEventListener('click', () => {
        mistype.parentNode.removeChild(mistype);
    });

    popup.appendChild(close);

    const closePopup = (e) => {
      if (e.keyCode === 27) {
        mistype.parentNode.removeChild(mistype);
      }

      document.removeEventListener('keydown', closePopup);
    }

    // Add ESC listener
    document.addEventListener('keydown', closePopup);
  }


  /**
   * Event listener on keydown
   */
  document.addEventListener('keydown', function (e) {
    if (!(e.key === 'Enter' && e.ctrlKey)) {
      return;
    }

    const selection = window.getSelection().toString();

    // Get only start container
    let container = window.getSelection().getRangeAt(0).startContainer;

    if (container.nodeType !== Node.ELEMENT_NODE) {
      container = container.parentElement;
    }

    const blocks = ['.entry-content > *', '.entry-header__title', '.entry-header__lead'];

    for (const block of blocks) {
      let ancestor = container;

      while (ancestor.parentElement) {
        ancestor = ancestor.parentElement;

        if (ancestor.matches(block)) {
          container = ancestor;
          break;
        }
      }
    }

    let context = container.textContent;

    if (context.length > 500) {
      context = context.substring(0, 500) + '…';
    }

    // If selection not empty
    if (selection.length > 0) {
      showPopup(selection.substring(0, 300), context);
    }
  });
})();
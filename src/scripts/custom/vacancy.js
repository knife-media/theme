(function () {
  /**
   * Check if custom options defined
   */
  if (typeof knife_theme_custom === 'undefined') {
    return false;
  }

  let content = document.querySelector('.entry-content');

  // Check if entry-content exists
  if (content === null) {
    return false;
  }

  // Check required ajaxurl
  if (typeof knife_theme_custom.ajaxurl === 'undefined') {
    return false;
  }


  /**
   * Helper to create DOM element
   */
  const buildElement = (tag, options) => {
    let element = document.createElement(tag);

    // Set single class
    if (options.hasOwnProperty('class')) {
      element.classList.add(options.class);
    }

    // Set class list
    if (options.hasOwnProperty('classes')) {
      options.classes.forEach(cl => {
        element.classList.add(cl);
      });
    }

    // Set textContent
    if (options.hasOwnProperty('text')) {
      element.textContent = options.text;
    }

    // Set innerHTML
    if (options.hasOwnProperty('html')) {
      element.innerHTML = options.html;
    }

    // Set attributes
    if (options.hasOwnProperty('attributes')) {
      for (let key in options.attributes) {
        element.setAttribute(key, options.attributes[key]);
      }
    }

    // Append child
    if (options.hasOwnProperty('parent')) {
      options.parent.appendChild(element);
    }

    return element;
  }


  /**
   * Draw fields form
   */
  const drawFields = (fields) => {
    let figure = buildElement('figure', {
      'classes': ['figure', 'figure--request'],
      'parent': content
    });

    // Create form
    let form = buildElement('form', {
      'parent': figure
    });

    // Update form size
    const resizeForm = (text) => {
      window.setTimeout(() => {
        text.style.height = 'auto';
        text.style.height = text.scrollHeight + 'px';
      }, 0);
    }

    let submit = buildElement('button', {
      'text': knife_theme_custom.button,
      'attributes': {
        'type': 'submit'
      }
    });

    for (let key in fields) {
      let text = buildElement('textarea', {
        'parent': form,
        'attributes': {
          'name': key,
          'required': 'required',
          'rows': 1,
          'placeholder': fields[key]
        }
      });

      text.addEventListener('keydown', (e) => {
        if (e.keyCode == 13 && (e.metaKey || e.ctrlKey)) {
          submit.click();
        }

        resizeForm(text);
      });

      // Resize on paste
      text.addEventListener('paste', () => {
        resizeForm(text);
      });
    }

    form.appendChild(submit);

    form.addEventListener('submit', (e) => {
      e.preventDefault();

      let data = {
        'nonce': knife_theme_custom.nonce,
        'time': knife_theme_custom.time,
        'fields': [],
        'formats': []
      };

      // Try to collect all formats
      let items = list.querySelectorAll('li');

      items.forEach(item => {
        if (item.hasAttribute('data-selected')) {
          data.formats.push(item.textContent);
        }
      });

      let inputs = form.querySelectorAll('textarea');

      inputs.forEach(input => {
        data.fields.push({
          'label': input.getAttribute('placeholder'),
          'value': input.value
        })
      });

      // Disable button
      submit.setAttribute('disabled', 'disabled');

      // Set button loader
      submit.setAttribute('data-loading', true);

      // Send request
      let request = new XMLHttpRequest();
      request.open('POST', knife_theme_custom.ajaxurl);
      request.setRequestHeader('Content-Type', 'application/json');

      // Check if loaded
      request.onload = function () {
        submit.removeAttribute('data-loading');

        if (request.status !== 200) {
          return submit.textContent = knife_theme_custom.error;
        }

        submit.textContent = knife_theme_custom.success;

        inputs.forEach(input => {
          input.value = '';
        });

        items.forEach(item => {
          item.removeAttribute('data-selected');
        })
      }

      request.onerror = function () {
        submit.removeAttribute('data-loading');

        // Show error on button
        submit.textContent = knife_theme_custom.error;
      }

      request.send(JSON.stringify(data));
    });
  }

  // Draw form
  if(knife_theme_custom.fields) {
    drawFields(knife_theme_custom.fields);
  }
})();

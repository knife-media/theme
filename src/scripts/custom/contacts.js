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

  const createOption = (option) => {
    const button = buildElement('button', {
      'classes': ['button', 'button--option'],
      'html': option.case,
      'parent': survey,
    });

    button.addEventListener('click', (e) => {
      e.preventDefault();

      while (survey.firstChild) {
        survey.removeChild(survey.lastChild);
      }

      buildElement('h5', {
        'html': option.case,
        'parent': survey,
      });

      buildElement('p', {
        'html': option.answer,
        'parent': survey,
      });

      start.textContent = knife_theme_custom.retry;
      survey.appendChild(start);
    });
  }

  const createField = (field) => {
    const button = buildElement('button', {
      'classes': ['button', 'button--option'],
      'html': field.case,
      'parent': survey,
    });

    button.addEventListener('click', (e) => {
      e.preventDefault();

      while (survey.firstChild) {
        survey.removeChild(survey.lastChild);
      }

      buildElement('h5', {
        'html': field.case,
        'parent': survey,
      });

      buildElement('h4', {
        'html': field.more,
        'parent': survey,
      });

      field.options.forEach((option, i) => {
        createOption(option);
      });
    });
  }

  let manage = buildElement('p', {
    'parent': content,
  });

  const start = buildElement('button', {
    'text': knife_theme_custom.start,
    'classes': ['button', 'button--start'],
    'parent': manage,
  });

  const survey = buildElement('figure', {
    'classes': ['figure', 'figure--survey'],
  });

  start.addEventListener('click', (e) => {
    e.preventDefault();

    while (survey.firstChild) {
      survey.removeChild(survey.lastChild);
    }

    if (null !== manage) {
      content.removeChild(manage);
    }

    manage = null;

    buildElement('h4', {
      'parent': survey,
      'html': knife_theme_custom.heading,
    });

    const fields = knife_theme_custom.fields;

    fields.forEach((field, i) => {
      createField(field);
    });

    content.appendChild(survey);
  });
})();

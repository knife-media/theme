(function () {
  // Check user form options existing
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


  // Declare global elements
  var form, notice, loader, submit;

  // Create single form field
  function createField(key, field, form) {
    if (typeof field.element === 'undefined') {
      return null;
    }

    var element = document.createElement(field.element);
    element.classList.add('form__field-' + field.element);
    element.setAttribute('name', key);

    var wrapper = document.createElement('div');
    wrapper.classList.add('form__field');
    wrapper.appendChild(element);

    delete field.element;

    for (var i in field) {
      element.setAttribute(i, field[i]);
    }

    element.value = getStorage(key);

    // Set save storage event listener
    element.addEventListener('input', function (e) {
      setStorage(this.name, this.value);
    });

    // Set unfold event listener
    element.addEventListener('focus', function (e) {
      form.classList.remove('form--fold');
    });

    return form.appendChild(wrapper);
  }


  // Set local storage input value
  function setStorage(name, value) {
    var storage = JSON.parse(localStorage.getItem('knife_form_write')) || {};

    storage[name] = value;

    return localStorage.setItem('knife_form_write', JSON.stringify(storage));
  }


  // Get local storage input value by name
  function getStorage(name) {
    var storage = JSON.parse(localStorage.getItem('knife_form_write')) || {};

    return storage[name] || '';
  }


  // Create form controls
  function appendControls(form) {
    var wrapper = document.createElement('div');
    wrapper.classList.add('form__control');

    loader = document.createElement('span');
    loader.classList.add('form__control-loader', 'icon');
    wrapper.appendChild(loader);

    notice = document.createElement('span');
    notice.classList.add('form__control-notice');
    wrapper.appendChild(notice);

    submit = document.createElement('button');
    submit.classList.add('form__control-button', 'button');
    submit.innerHTML = getOption('button', 'Send');
    wrapper.appendChild(submit);

    return form.appendChild(wrapper);
  }


  // Control event before and after request
  function requestEvent(stop) {
    loader.classList.remove('icon--loop');

    if (typeof stop === 'undefined' || stop === true) {
      return submit.removeAttribute('disabled', '');
    }

    loader.classList.remove('icon--alert', 'icon--done');
    loader.classList.add('icon--loop');

    notice.innerHTML = '';

    return submit.setAttribute('disabled', '');
  }


  // Show form errors
  function displayWarning(message) {
    var message = message || getOption('warning', 'Request error');

    loader.classList.add('icon--alert');
    notice.innerHTML = message;
  }


  // Show form errors
  function displaySuccess(message) {
    var message = message || '';

    loader.classList.add('icon--done');
    notice.innerHTML = message;

    localStorage.removeItem('knife_form_write');

    return form.reset();
  }


  // Submit form event
  function submitForm(e) {
    e.preventDefault();

    let data = {
      'nonce': getOption('nonce'),
      'time': getOption('time'),
      'name': form.querySelector('input[name="name"]').value,
      'email': form.querySelector('input[name="email"]').value,
      'subject': form.querySelector('input[name="subject"]').value,
      'text': form.querySelector('textarea[name="text"]').value,
    };


    let request = new XMLHttpRequest();
    request.open('POST', knife_theme_custom.ajaxurl + '/club');
    request.setRequestHeader('Content-Type', 'application/json');
    request.send(JSON.stringify(data));

    request.onload = function () {
      requestEvent(true);

      if (request.status !== 200) {
        return displayWarning();
      }

      var response = JSON.parse(request.responseText);

      if (response.success) {
        return displaySuccess(response.message);
      }

      return displayWarning(response.message);
    }

    return requestEvent(false);
  }


  // Get option from global settings
  function getOption(option, alternate) {
    if (knife_theme_custom.hasOwnProperty(option)) {
      return knife_theme_custom[option];
    }

    return alternate || '';
  }


  // Append fields to form
  function createFields(form) {
    var fields = knife_theme_custom.fields;

    for (var key in fields) {
      if (!fields.hasOwnProperty(key)) {
        continue;
      }

      createField(key, fields[key], form);
    }

    return appendControls(form);
  }

  // Create form element
  var form = document.createElement('form');

  // Find post element
  var post = document.querySelector('.entry-content');
  post.appendChild(form);

  // Set custom form class
  var classes = getOption('classes');
  if (classes.length > 0) {
    form.className = classes.join(' ');
  }

  // Set form title
  var heading = getOption('heading');
  if (heading.length > 0) {
    var title = document.createElement('h4');
    title.classList.add('form__heading');
    title.textContent = heading;
    form.appendChild(title);
  }

  form.classList.add('form', 'form--club');
  form.addEventListener('submit', submitForm);

  return createFields(form);
})();
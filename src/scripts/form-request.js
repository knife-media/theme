/**
 * User requests form
 *
 * @since 1.7
 */

(function() {
  // Check user form options existing
  if(typeof knife_form_request === 'undefined') {
    return false;
  }


  // Declare global elements
  var form, notice, loader, submit;

  // Create single form field
  function createField(key, field, form) {
    if(typeof field.element === 'undefined') {
      return null;
    }

    var element = document.createElement(field.element);
    element.classList.add('form__field-' + field.element);
    element.setAttribute('name', key);

    var wrapper = document.createElement('div');
    wrapper.classList.add('form__field');
    wrapper.appendChild(element);

    delete field.element;

    for(var i in field) {
      element.setAttribute(i, field[i]);
    }

    element.value = getStorage(key);

    // Set save storage event listener
    element.addEventListener('input', function(e) {
      setStorage(this.name, this.value);
    });

    // Set unfold event listener
    element.addEventListener('focus', function(e) {
      form.classList.remove('form--fold');
    });

    return form.appendChild(wrapper);
  }


  // Set local storage input value
  function setStorage(name, value) {
    var storage = JSON.parse(localStorage.getItem('knife_form_request')) || {};

    storage[name] = value;

    return localStorage.setItem('knife_form_request', JSON.stringify(storage));
  }


  // Get local storage input value by name
  function getStorage(name) {
    var storage = JSON.parse(localStorage.getItem('knife_form_request')) || {};

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

    if(typeof stop === 'undefined' || stop === true) {
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

    localStorage.removeItem('knife_form_request');

    return form.reset();
  }


  // Submit form event
  function submitForm(e) {
    e.preventDefault();

    var formData = new FormData(form);

    // First of all append required params
    var postData = 'action=' + getOption('action') + '&nonce=' + getOption('nonce');

    // Get params from post fields
    formData.forEach(function(value, key) {
      postData += '&' + key + '=' + value;
    });


    // Send request
    var request = new XMLHttpRequest();
    request.open('POST', getOption('ajaxurl'));
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=utf-8');

    request.onload = function() {
      requestEvent(true);

      if(request.status !== 200) {
        return displayWarning();
      }

      var response = JSON.parse(request.responseText);

      if(response.success) {
        return displaySuccess(response.data);
      }

      return displayWarning(response.data);
    }

    request.send(postData);

    return requestEvent(false);
  }


  // Get option from global settings
  function getOption(option, def) {
    if(knife_form_request.hasOwnProperty(option)) {
      return knife_form_request[option];
    }

    return def || '';
  }


  // Append fields to form
  function createFields(form) {
    var fields = knife_form_request.fields;

    for(var key in fields) {
      if(!fields.hasOwnProperty(key)) {
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
  if(classes.length > 0) {
    form.className = classes.join(' ');
  }

  // Set form title
  var heading = getOption('heading');
  if(heading.length > 0) {
    var title = document.createElement('h3');
    title.classList.add('form__heading');
    title.textContent = heading;
    form.appendChild(title);
  }

  form.classList.add('form');
  form.addEventListener('submit', submitForm);

  return createFields(form);
})();

(function() {
  // Check slider element and options meta
  if(typeof knife_user_form === 'undefined') {
    return false;
  }


  // Declare global elements
  var form, notice, loader, submit;

  // Create single form field
  var createField = function(key, field, form) {
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

    element.addEventListener('input', function(e) {
      return setStorage(this.name, this.value);
    });

    return form.appendChild(wrapper);
  }


  // Set local storage input value
  var setStorage = function(name, value) {
    var storage = JSON.parse(localStorage.getItem('knife_user_form')) || {};

    storage[name] = value;

    return localStorage.setItem('knife_user_form', JSON.stringify(storage));
  }


  // Get local storage input value by name
  var getStorage = function(name) {
    var storage = JSON.parse(localStorage.getItem('knife_user_form')) || {};

    return storage[name] || '';
  }


  // Create form controls
  var appendControls = function(form) {
    var wrapper = document.createElement('div');
    wrapper.classList.add('form__control');

    submit = document.createElement('button');
    submit.classList.add('form__control-button', 'button');
    submit.innerHTML = getOption('button', 'Send');
    wrapper.appendChild(submit);

    loader = document.createElement('span');
    loader.classList.add('form__control-loader', 'icon');
    wrapper.appendChild(loader);

    notice = document.createElement('span');
    notice.classList.add('form__control-notice');
    wrapper.appendChild(notice);

    return form.appendChild(wrapper);
  }


  // Control event before and after request
  var requestEvent = function(stop) {
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
  var displayWarning = function(message) {
    var message = message || getOption('warning', 'Request error');

    loader.classList.add('icon--alert');
    notice.innerHTML = message;
  }


  // Show form errors
  var displaySuccess = function(message) {
    var message = message || 'All done';

    loader.classList.add('icon--done');
    notice.innerHTML = message;

    localStorage.removeItem('knife_user_form');

    return form.reset();
  }


  // Submit form event
  var submitForm = function(e) {
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
  var getOption = function(option, def) {
    if(knife_user_form.hasOwnProperty(option)) {
      return knife_user_form[option];
    }

    return def || '';
  }


  //  Append fields to form
  var createForm = function(form) {
    var fields = knife_user_form.fields;

    for(var key in fields) {
      if(!fields.hasOwnProperty(key)) {
        continue;
      }

      createField(key, fields[key], form);
    }

    return appendControls(form);
  }

  // Find post element
  var post = document.querySelector('.entry-content');

  var form = document.createElement('form');
  form.classList.add('form', 'form--club');
  form.addEventListener('submit', submitForm);
  post.appendChild(form);

  return createForm(form);
})();

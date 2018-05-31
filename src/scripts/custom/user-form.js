(function() {
  // Check slider element and options meta
  if(typeof knife_user_form === 'undefined')
    return false;


  var form = null;

  var createField = function(key, field, form) {
    if(typeof field.element === 'undefined')
      return null;

    var el = document.createElement(field.element);
    el.classList.add('form__field', 'form__field--' + field.element);
    el.setAttribute('name', key);

    if(typeof field.placeholder !== 'undefined')
      el.setAttribute('placeholder', field.placeholder);

    if(typeof field.type !== 'undefined')
      el.setAttribute('type', field.type);

    if(typeof field.value !== 'undefined')
      el.innerHTML = field.value;


    return form.appendChild(el);
  }


  var submitForm = function(e) {
    e.preventDefault();

    var formData = new FormData(form);
    var postData = 'action=' + knife_user_form.action;

    formData.forEach(function(value, key){
      postData += '&' + key + '=' + value;
    });


    console.log(postData);

    var request = new XMLHttpRequest();
    request.open('POST', ajaxurl);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=utf-8');
    request.send(postData);
  }


  //  Append fields to form
  var appendForm = function(form) {
    var fields = knife_user_form.fields;

    for(var key in fields) {
      if(!fields.hasOwnProperty(key))
        continue;

      createField(key, fields[key], form);
    }
  }


  // Find post element
  var post = document.querySelector('.post');

  var form = document.createElement('form');
  form.classList.add('post__form', 'form');
  form.addEventListener('submit', submitForm);
  post.appendChild(form);

  return appendForm(form);
})();

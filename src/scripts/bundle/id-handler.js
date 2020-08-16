/**
 * Comments and profiles handler
 *
 * @since 1.13
 */

(function () {
  let comments = document.getElementById('comments');

  if (comments === null) {
    return false;
  }


  // Check id handler options existing
  if (knife_id_handler === undefined) {
    return false;
  }


  /**
   * Get option from global settings
   */
  const getOption = (option, alternate) => {
    const args = option.split('.');

    if (args.length > 1) {
      const [group, name] = args;

      if (knife_id_handler[group][name] !== undefined) {
        return knife_id_handler[group][name];
      }
    }

    if (knife_id_handler[option] !== undefined) {
      return knife_id_handler[option];
    }

    return alternate || '';
  }

  // All availible oauth providers
  const providers = ['vkontakte', 'google', 'facebook', 'yandex'];


  const post = getOption('post', null);

  // Check if required post option exists
  if (post === null) {
    return false;
  }


  // Store is user logged in
  let authorized = false;


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
   * Scroll to element
   */
  const scrollToElement = (top) => {
    let offset = top + window.pageYOffset - 24;

    // Get styicky header
    let header = document.querySelector('.header');

    // Check sticky header height
    if (header !== null) {
      let styles = window.getComputedStyle(header);

      // Add header height to offset
      offset = offset - parseInt(styles.getPropertyValue('height'));
    }

    // Try to scroll smoothly
    if ('scrollBehavior' in document.documentElement.style) {
      return window.scrollTo({
        top: offset,
        behavior: 'smooth'
      });
    }

    window.scrollTo(0, offset);
  }

  /**
   * Save unsent text field
   */
  const saveUnsent = (text, reply) => {
    let storage = JSON.parse(localStorage.getItem('knife_id_unsent')) || {};

    storage[post] = {
      'text': text,
      'reply': reply
    };

    // Set for current post
    localStorage.setItem('knife_id_unsent', JSON.stringify(storage));
  }

  /**
   * Delete unsent text field
   */
  const deleteUnsent = () => {
    let storage = JSON.parse(localStorage.getItem('knife_id_unsent')) || {};

    delete storage[post];

    // Set for current post
    localStorage.setItem('knife_id_unsent', JSON.stringify(storage));
  }

  /**
   * Get unsent text field
   */
  const getUnsent = (reply) => {
    let storage = JSON.parse(localStorage.getItem('knife_id_unsent')) || {};

    // Get unset object
    let unsent = storage[post] || {};

    if (unsent.reply === reply) {
      return unsent.text || '';
    }

    return '';
  }


  /**
   * Convert date
   */
  const convertDate = (moment) => {
    let time = new Date(moment);

    let options = {
      hour: 'numeric',
      minute: 'numeric',
      month: 'numeric',
      day: 'numeric'
    }

    // Add year for old dates
    if (time.getFullYear() < new Date().getFullYear()) {
      options.year = 'numeric';
    }

    return time.toLocaleString("ru", options);
  }


  /**
   * Draw expand button
   */
  const foldComments = () => {
    comments.classList.add('comments--folded');

    let expand = buildElement('div', {
      'class': 'comments__expand',
      'parent': comments
    });

    let button = buildElement('button', {
      'class': 'comments__expand-button',
      'text': getOption('comments.expand'),
      'attrubutes': {
        'type': 'button'
      },
      'parent': expand
    });

    button.addEventListener('click', (e) => {
      e.preventDefault();

      comments.classList.remove('comments--folded');
    });
  }


  /**
   * Draw comment avatar
   */
  const drawAvatar = (parent, field) => {
    let image = new Image();
    parent.appendChild(image);

    // Get dumb avatar from options
    let noavatar = getOption('comments.noavatar');

    image.onerror = () => {
      image.setAttribute('src', noavatar);
    }

    image.onload = () => {
      image.removeAttribute('loading');
    }

    // Set image attributes
    image.setAttribute('loading', true);
    image.setAttribute('src', field.avatar);
    image.setAttribute('alt', field.name);
  }

  /**
   * Draw comment time
   */
  const drawTime = (parent, field) => {
    let time = buildElement('a', {
      'class': 'comments__item-time',
      'text': convertDate(field.created),
      'attributes': {
        'href': `#comment-${field.id}`
      },
      'parent': parent
    });

    // Add time click event
    time.addEventListener('click', (e) => {
      e.preventDefault();

      // Set url hash without jumping
      history.pushState({}, '', time.href);

      // Show comments
      comments.classList.remove('comments--folded');

      // Scroll to element
      scrollToElement(parent.getBoundingClientRect().top);
    });
  }


  /**
   * Draw replied comment info
   */
  const drawReplied = (parent, field, item) => {
    if (!parent.hasAttribute('data-name')) {
      return item;
    }

    let replied = buildElement('a', {
      'class': 'comments__item-replied',
      'html': `<span class="icon icon--reply"></span>`,
      'attributes': {
        'href': `#comment-${field.parent}`
      },
      'parent': item.querySelector('.comments__item-header')
    });

    buildElement('span', {
      'text': parent.getAttribute('data-name'),
      'parent': replied
    });

    replied.addEventListener('click', (e) => {
      e.preventDefault();

      // Set url hash without jumping
      history.pushState({}, '', replied.href);

      // Show comments
      comments.classList.remove('comments--folded');

      // Scroll to element
      scrollToElement(parent.getBoundingClientRect().top);
    });

    return item;
  }


  /**
   * Draw comment reply button
   */
  const drawReply = (parent, field, item) => {
    let reply = buildElement('button', {
      'class': 'comments__item-button',
      'text': getOption('comments.reply'),
      'parent': parent
    });

    reply.addEventListener('click', (e) => {
      e.preventDefault();

      // Check if authorized
      if (!authorized) {
        return showLogin();
      }

      // Show comments
      comments.classList.remove('comments--folded');

      // Try to remove another child forms
      comments.querySelectorAll('.comments__item-form').forEach((el) => {
        el.parentNode.removeChild(el);
      });

      let form = createForm();
      form.classList.add('comments__item-form');
      form.setAttribute('data-reply', field.id);

      // Append form after comment
      item.insertBefore(form, parent.nextSibling);

      // Create cancel reply button
      let cancel = buildElement('button', {
        'class': 'comments__item-cancel',
        'text': getOption('form.cancel'),
        'parent': form.querySelector('.comments__form-footer')
      });

      cancel.addEventListener('click', (e) => {
        e.preventDefault();

        return form.parentNode.removeChild(form);
      });

      // Get form textarea
      let text = form.querySelector('.comments__form-text');
      text.setAttribute('placeholder', getOption('form.reply'));

      // Get saved textarea value
      text.value = getUnsent(field.id);
      text.focus();

      text.addEventListener('input', () => {
        saveUnsent(text.value, field.id);
      });
    });
  }


  /**
   * Draw comment remove button
   */
  const drawRemove = (parent, field, item) => {
    let remove = buildElement('button', {
      'class': 'comments__item-button',
      'text': getOption('comments.remove'),
      'parent': parent
    });

    remove.addEventListener('click', (e) => {
      e.preventDefault();

      let url = `/id/comments/${field.id}`;

      makeRequest(url, 'DELETE', {}, () => {
        let children = item.querySelector('.comments__item-children');

        while (item.lastChild) {
          item.removeChild(item.lastChild)
        }

        // Draw removed warning
        drawWarning(item, 'removed');

        if (children !== null) {
          item.appendChild(children);
        }
      });
    });

    return item;
  }


  /**
   * Draw warning
   */
  const drawWarning = (item, status) => {
    let message = buildElement('div', {
      'class': 'comments__item-warning',
      'parent': item
    })

    if (status === 'removed') {
      message.textContent = getOption('comments.removed');
    }

    if (status === 'blocked') {
      message.textContent = getOption('comments.blocked');
    }

    return item;
  }


  /**
   * Draw user block button
   */
  const drawManage = (parent, field, item) => {
    let remove = buildElement('button', {
      'class': 'comments__item-button',
      'text': getOption('comments.remove'),
      'parent': parent
    });

    remove.addEventListener('click', (e) => {
      e.preventDefault();

      let data = {
        'remove': field.id
      };

      sendAjax(data, remove, () => {
        let children = item.querySelector('.comments__item-children');

        while (item.lastChild) {
          item.removeChild(item.lastChild)
        }

        // Draw removed warning
        drawWarning(item, 'removed');

        if (children !== null) {
          item.appendChild(children);
        }
      });
    });


    let block = buildElement('button', {
      'class': 'comments__item-button',
      'text': getOption('comments.block'),
      'parent': parent
    });

    block.addEventListener('click', (e) => {
      e.preventDefault();

      let data = {
        'block': field.id
      };

      sendAjax(data, block, () => {
        let children = item.querySelector('.comments__item-children');

        while (item.lastChild) {
          item.removeChild(item.lastChild)
        }

        // Draw removed warning
        drawWarning(item, 'blocked');

        if (children !== null) {
          item.appendChild(children);
        }
      });
    });

    return item;
  }


  /**
   * Draw votes block
   */
  const updateVotes = (parent, field) => {
    let rating = parseInt(field.plus) - parseInt(field.minus);

    // Reset class names
    parent.className = 'comments__item-vote';

    // Disable votes for self comments
    if (field.self) {
      parent.classList.add('comments__item-vote--voted');
    }

    if (rating > 0) {
      parent.classList.add('comments__item-vote--plus');
    }

    if (rating < 0) {
      parent.classList.add('comments__item-vote--minus');
    }

    let plus = buildElement('button', {
      'classes': ['icon', 'icon--vote'],
      'parent': parent
    });

    // Check voted for plus
    if (field.vote === 'plus') {
      plus.classList.remove('icon--vote');
      plus.classList.add('icon--voted');

      parent.classList.add('comments__item-vote--voted');
    }

    // Create count
    buildElement('span', {
      'text': rating,
      'attributes': {
        'title': `+${field.plus} / -${field.minus}`
      },
      'parent': parent
    });

    let minus = buildElement('button', {
      'classes': ['icon', 'icon--vote'],
      'parent': parent
    });

    // Check voted for minus
    if (field.vote === 'minus') {
      minus.classList.remove('icon--vote');
      minus.classList.add('icon--voted');

      parent.classList.add('comments__item-vote--voted');
    }

    // Capture click on plus
    plus.addEventListener('click', (e) => {
      e.preventDefault();

      // Check if authorized
      if (!authorized) {
        return showLogin();
      }

      // Check if current vote is plus
      if (plus.classList.contains('icon--voted')) {
        // Update comment fields
        field.plus = parseInt(field.plus) - 1;
        delete field.vote;

        makeRequest(`/id/ratings?comment=${field.id}&vote=plus`, 'DELETE');
      }

      // Check if current vote is not plus
      if (plus.classList.contains('icon--vote')) {
        // Update comment fields
        field.plus = parseInt(field.plus) + 1;
        field.vote = 'plus';

        makeRequest(`/id/ratings?comment=${field.id}&vote=plus`, 'POST');
      }

      // Clear child nodes
      while (parent.lastChild) {
        parent.removeChild(parent.lastChild);
      }

      // Redraw votes block
      updateVotes(parent, field);
    });

    // Capture click on minus
    minus.addEventListener('click', (e) => {
      e.preventDefault();

      // Check if authorized
      if (!authorized) {
        return showLogin();
      }

      // Check if current vote is minus
      if (minus.classList.contains('icon--voted')) {
        // Update comment fields
        field.minus = parseInt(field.minus) - 1;
        delete field.vote;

        makeRequest(`/id/ratings?comment=${field.id}&vote=minus`, 'DELETE');
      }

      // Check if current vote is not minus
      if (minus.classList.contains('icon--vote')) {
        // Update comment fields
        field.minus = parseInt(field.minus) + 1;
        field.vote = 'minus';

        makeRequest(`/id/ratings?comment=${field.id}&vote=minus`, 'POST');
      }

      // Clear child nodes
      while (parent.lastChild) {
        parent.removeChild(parent.lastChild);
      }

      // Redraw votes block
      updateVotes(parent, field);
    });
  }


  /**
   * Draw comment
   */
  const drawComment = (item, field) => {
    item.setAttribute('data-id', field.id);

    // Hide comment if not visible
    if (field.status !== 'visible') {
      return drawWarning(item, field.status);
    }

    // Set default name
    field.name = field.name || getOption('comments.anonymous');

    // Update content field
    field.content = field.content.replace(/(\n)+/g, '<br>');

    // Set name to comment for replied block
    item.setAttribute('data-name', field.name);

    // Create avatar wrapper
    let avatar = buildElement('div', {
      'class': 'comments__item-avatar',
      'parent': item
    });

    // Draw avatar
    drawAvatar(avatar, field);

    // Create header
    let header = buildElement('div', {
      'class': 'comments__item-header',
      'parent': item
    });

    // Draw time
    drawTime(item, field);

    // Create name
    buildElement('span', {
      'class': 'comments__item-name',
      'text': field.name,
      'parent': header
    });

    // Create content
    buildElement('span', {
      'class': 'comments__item-content',
      'html': field.content,
      'parent': item
    });

    // Create footer
    let footer = buildElement('div', {
      'class': 'comments__item-footer',
      'parent': item
    });

    // Show reply button
    drawReply(footer, field, item);

    // Create vote element
    let vote = buildElement('div', {
      'class': 'comments__item-vote',
      'parent': footer
    });

    // Update votes block
    updateVotes(vote, field)

    // Show manage buttons
    if (getOption('action')) {
      return drawManage(footer, field, item);
    }

    // Remove button for user comments
    if (field.self) {
      drawRemove(footer, field, item);
    }

    return item;
  }


  /**
   * Show comment
   */
  const loadComment = (field) => {
    let item = buildElement('div', {
      'class': 'comments__item'
    });

    // Append comment elements to item
    item = drawComment(item, field);

    if (field.parent === null) {
      return comments.appendChild(item);
    }

    // Try to find parent
    let parent = comments.querySelector(`[data-id="${field.parent}"]`);

    if (parent === null) {
      return comments.appendChild(item);
    }

    // Find or create children list
    let children = parent.querySelector('.comments__item-children');

    if (children === null) {
      children = buildElement('div', {
        'class': 'comments__item-children',
        'parent': parent
      });
    }

    if (field.status === 'visible') {
      item = drawReplied(parent, field, item);
    }

    // Get parent level from attribute
    let level = parent.getAttribute('data-level') || 0;

    // Increase level
    level = parseInt(level) + 1;

    // Set item level attribute
    item.setAttribute('data-level', level);

    // Set level class for deep comments
    if (level > 2) {
      children.classList.add('comments__item-children--deepest');
    }

    return children.appendChild(item);
  }


  /**
   * Send form event
   */
  const sendForm = (form) => {
    let url = `/id/comments?post=${post}`;

    // Get parent comment id
    let reply = form.getAttribute('data-reply');

    if (reply) {
      url = url + `&parent=${reply}`;
    }

    // Get form text field
    let data = {
      'content': form.querySelector('textarea').value
    }

    makeRequest(url, 'POST', data, (response) => {
      let fields = response.comments || [];

      if (fields.length > 0) {
        form.reset();

        if (reply) {
          form.parentNode.removeChild(form);
        }

        let item = loadComment(fields[0], true);

        // Show comments
        comments.classList.remove('comments--folded');

        // Scroll to it
        scrollToElement(item.getBoundingClientRect().top);
      }

      return deleteUnsent();
    });
  }


  /**
   * Create form
   */
  const createForm = () => {
    let form = buildElement('form', {
      'class': 'comments__form'
    });

    let text = buildElement('textarea', {
      'class': 'comments__form-text',
      'attributes': {
        'placeholder': getOption('form.placeholder'),
      },
      'parent': form
    });

    let label = getOption('form.authorize');

    if (authorized) {
      label = getOption('form.submit');

      // Disable non-empty textarea for authorized
      text.setAttribute('required', 'required');
    }

    let footer = buildElement('div', {
      'class': 'comments__form-footer',
      'parent': form
    });

    let submit = buildElement('button', {
      'classes': ['comments__form-submit', 'button'],
      'attributes': {
        'type': 'submit'
      },
      'text': label,
      'parent': footer
    });

    // Submit form listener
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      if (!authorized) {
        return showLogin();
      }

      // Send form event
      sendForm(form);
    });

    // Update form size
    const resizeForm = () => {
      window.setTimeout(() => {
        text.style.height = 'auto';
        text.style.height = `${text.scrollHeight + 2}px`;
      }, 0);
    }

    // Submit and resize textarea
    text.addEventListener('keydown', (e) => {
      if (e.keyCode == 13 && (e.metaKey || e.ctrlKey)) {
        submit.click();
      }

      resizeForm();
    });

    // Resize on paste
    text.addEventListener('paste', resizeForm);

    return form;
  }


  /**
   * Show error after request
   */
  const showError = (message) => {
    message = message || getOption('error');

    if (!comments.classList.contains('comments--expand')) {
      return console.error(message);
    }

    let form = comments.querySelector('.comments__form');

    if (form === null) {
      return console.error(message);
    }

    let error = comments.querySelector('.comments__error');

    if (error === null) {
      error = buildElement('p', {
        'class': 'comments__error'
      });
    }

    error.textContent = message;

    // Add error after form
    form.parentNode.insertBefore(error, form.nextSibling);

    // Scroll to error
    scrollToElement(form.getBoundingClientRect().top);
  }


  /**
   * Send AJAX request to WordPress
   */
  const sendAjax = (fields, button, callback) => {
    let formData = new FormData();
    formData.append('action', getOption('action'));
    formData.append('nonce', getOption('nonce'));

    // Add form fields
    for (let key in fields) {
      formData.append(key, fields[key]);
    }

    // Disable button before request
    button.setAttribute('disabled', 'disabled');

    // Send request
    let request = new XMLHttpRequest();
    request.open('POST', getOption('ajaxurl'));
    request.send(formData);

    request.onload = () => {
      let error = comments.querySelector('.comments__error');

      if (error !== null) {
        comments.removeChild(error);
      }

      // Enable button
      button.removeAttribute('disabled');

      try {
        let response = JSON.parse(request.responseText);

        if (!response.success) {
          return showError(response.data);
        }

        // Return callback if exists
        if (typeof callback === 'function') {
          return callback(response.data || {});
        }

      } catch (err) {
        return showError();
      }
    }
  }


  /**
   * Send request to API
   */
  const makeRequest = (url, method, data, callback) => {
    const request = new XMLHttpRequest();
    request.open(method, url, true);
    request.setRequestHeader("Content-Type", "application/json");

    request.onload = () => {
      let error = comments.querySelector('.comments__error');

      if (error !== null) {
        comments.removeChild(error);
      }

      try {
        let response = JSON.parse(request.responseText);

        if (request.status !== 200) {
          return showError(response.message || getOption('error'));
        }

        // Return callback if exists
        if (typeof callback === 'function') {
          return callback(response.result || {});
        }

      } catch (err) {
        return showError();
      }
    }

    if (data === undefined) {
      data = {};
    }

    request.send(JSON.stringify(data));
  }


  /**
   * Create badge in form
   */
  const createBadge = (form, field) => {
    if (field === undefined) {
      return form;
    }

    let badge = buildElement('div', {
      'class': 'comments__form-badge',
      'parent': form.querySelector('.comments__form-footer')
    });

    // Draw avatar
    drawAvatar(badge, field);

    // Append name
    buildElement('span', {
      'text': field.name,
      'parent': badge
    });

    // Create exit button
    let exit = buildElement('button', {
      'classes': ['comments__form-exit', 'icon', 'icon--exit'],
      'attributes': {
        'type': 'button',
        'title': getOption('form.exit')
      },
      'parent': form
    });

    exit.addEventListener('click', (e) => {
      e.preventDefault();

      // Hide comments right now
      comments.classList.remove('comments--expand');

      makeRequest('/id/logout', 'POST', {}, () => {
        setTimeout(initComments, 300);
      });
    });

    return form;
  }


  /**
   * Create authentication popup
   */
  const showLogin = () => {
    let login = buildElement('div', {
      'class': 'login'
    })

    // Create popup modal
    let popup = buildElement('div', {
      'class': 'login__popup',
      'parent': login
    });

    // Add popup title
    buildElement('h3', {
      'class': 'login__popup-heading',
      'text': getOption('login.heading'),
      'parent': popup
    });

    // Add header helper
    buildElement('p', {
      'class': 'login__popup-helper',
      'text': getOption('login.helper'),
      'parent': popup
    });

    // Add buttons
    providers.forEach(provider => {
      let button = buildElement('a', {
        'classes': ['login__popup-button', `login__popup-button--${provider}`],
        'text': getOption(`login.${provider}`),
        'attributes': {
          'href': `/id/profiles/${provider}`,
          'target': '_blank'
        },
        'parent': popup
      });

      buildElement('span', {
        'classes': ['icon', `icon--${provider}`],
        'parent': button
      });
    });

    // Add policy description
    buildElement('p', {
      'class': 'login__popup-policy',
      'html': getOption('login.policy'),
      'parent': popup
    });

    // Add close button
    let close = buildElement('button', {
      'class': 'login__popup-close',
      'parent': popup
    });

    close.addEventListener('click', () => {
      document.body.removeChild(login);

      // Remove listener here
      document.removeEventListener('keydown', closeLogin);
    });

    // Self removed close login function
    const closeLogin = (e) => {
      if (e.keyCode === 27) {
        document.removeEventListener('keydown', closeLogin);

        // Remove login popup
        document.body.removeChild(login);
      }
    }

    // Add ESC listener
    document.addEventListener('keydown', closeLogin);

    // Self removed login listener
    const receiveLogin = (e) => {
      if (e.data === 'reload') {
        window.removeEventListener('message', receiveLogin);

        // Remove login popup
        document.body.removeChild(login);

        return initComments();
      }
    }

    // Listen to message from
    window.addEventListener('message', receiveLogin);

    // Append login popup on page
    document.body.appendChild(login);
  }


  /**
   * Load comments
   */
  const initComments = () => {
    // Clear old comments first
    while (comments.lastChild) {
      comments.removeChild(comments.lastChild)
    }

    authorized = false;

    makeRequest(`/id/comments?post=${post}`, 'GET', {}, (response) => {
      const fields = response.comments || [];

      // Check if user authorized
      if (response.identity) {
        authorized = true;
      }

      // Create form
      let form = createForm();
      comments.appendChild(form);

      // Get saved textarea value
      let text = form.querySelector('.comments__form-text');
      text.textContent = getUnsent(0);

      text.addEventListener('input', () => {
        saveUnsent(text.value, 0);
      });

      // Try to show identity bage
      createBadge(form, response.identity);

      // Fold comments if more than 10
      if (fields.length > 10) {
        foldComments();
      }

      // Show comments using response fields
      fields.forEach(field => {
        if (field.id !== undefined) {
          loadComment(field);
        }
      });

      // Show comments
      comments.classList.add('comments--expand');

      // Get comment link if exists
      const hash = document.location.hash.match(/^#comment-(\d+)/) || [];

      if (hash.length > 1) {
        let item = comments.querySelector(`[data-id="${hash[1]}"]`);

        if (item !== null) {
          // Show comments
          comments.classList.remove('comments--folded');

          // Scroll to comment
          scrollToElement(item.getBoundingClientRect().top);
        }
      }
    });
  }


  /**
   * Ok, let's go
   */
  return initComments();
})();
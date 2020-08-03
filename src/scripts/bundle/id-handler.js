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
  if (typeof knife_id_handler === 'undefined') {
    return false;
  }


  /**
   * Get option from global settings
   */
  const getOption = (option, alternate) => {
    if (knife_id_handler.hasOwnProperty(option)) {
      return knife_id_handler[option];
    }

    return alternate || '';
  }


  let post = getOption('post', null);

  // Check if required post option exists
  if (post === null) {
    return false;
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

    window.scrollTo(to, 0);
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
   * Draw comment avatar
   */
  const drawAvatar = (parent, field) => {
    let avatar = document.createElement('div');
    avatar.classList.add('comments__item-avatar');
    parent.appendChild(avatar);

    // Try to get image
    let image = new Image();

    image.onerror = () => {
      image.src = getOption('avatar');
    }

    // Set image attributes
    image.src = field.avatar || getOption('avatar');

    avatar.appendChild(image);
  }

  /**
   * Draw comment time
   */
  const drawTime = (parent, field, item) => {
    let time = document.createElement('a');
    time.classList.add('comments__item-time');
    time.textContent = convertDate(field.created);
    time.setAttribute('href', `#comment-${field.id}`);

    // Add time click event
    time.addEventListener('click', () => {
      scrollToElement(item.getBoundingClientRect().top);
    });

    parent.appendChild(time);
  }


  /**
   * Draw comment reply button
   */
  const drawReply = (parent, field, item) => {
    let reply = document.createElement('button');
    reply.classList.add('comments__item-button');
    reply.textContent = getOption('reply');

    reply.addEventListener('click', (e) => {
      e.preventDefault();

      // Close form if opened
      let form = item.querySelector('.comments__item-form');

      if (form) {
        return form.remove();
      }

      // Try to remove another child forms
      comments.querySelectorAll('.comments__item-form').forEach((el) => {
        el.remove();
      });

      form = createForm();
      form.classList.add('comments__item-form');
      form.setAttribute('data-reply', field.id);

      // Append form after comment
      item.insertBefore(form, parent.nextSibling);

      // Set focus to form
      let text = form.querySelector('textarea');

      // Set name to form
      let name = field.name.split(' ');
      text.value = `${name[0]}, `;
      text.focus();
    });

    parent.appendChild(reply);
  }


  /**
   * Draw comment remove button
   */
  const drawRemove = (parent, field, item) => {
    let remove = document.createElement('button');
    remove.classList.add('comments__item-button');
    remove.textContent = getOption('remove');

    remove.addEventListener('click', (e) => {
      e.preventDefault();

      if (confirm('Уверены, что хотите удалить комментарий?')) {
        item.remove();
      }
    })

    parent.appendChild(remove);
  }


  /**
   * Draw user block button
   */
  const drawManage = (parent, field) => {
    let remove = document.createElement('button');
    remove.classList.add('comments__item-button');
    remove.textContent = getOption('remove');
    parent.appendChild(remove);

    remove.addEventListener('click', (e) => {
      e.preventDefault();
    });


    let block = document.createElement('button');
    block.classList.add('comments__item-button');
    block.textContent = getOption('block');
    parent.appendChild(block);

    block.addEventListener('click', (e) => {
      e.preventDefault();
    });
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

    let plus = document.createElement('button');
    plus.classList.add('icon', 'icon--vote');
    parent.appendChild(plus);

    // Check voted for plus
    if (field.vote === 'plus') {
      plus.classList.remove('icon--vote');
      plus.classList.add('icon--voted');

      parent.classList.add('comments__item-vote--voted');
    }

    let count = document.createElement('span');
    count.textContent = rating;
    count.setAttribute('title', `+${field.plus} / -${field.minus}`);
    parent.appendChild(count);

    let minus = document.createElement('button');
    minus.classList.add('icon', 'icon--vote');
    parent.appendChild(minus);

    // Check voted for minus
    if (field.vote === 'minus') {
      minus.classList.remove('icon--vote');
      minus.classList.add('icon--voted');

      parent.classList.add('comments__item-vote--voted');
    }

    // Capture click on plus
    plus.addEventListener('click', (e) => {
      e.preventDefault();

      // Check if current vote is plus
      if (plus.classList.contains('icon--voted')) {
        // Update comment fields
        field.plus = parseInt(field.plus) - 1;
        delete field.vote;

        let url = `/id/ratings?comment=${field.id}&vote=plus`;

        makeRequest(url, 'DELETE', {}, (response) => {
          if (response.message) {
            console.error(response.message);
          }
        });
      }

      // Check if current vote is not plus
      if (plus.classList.contains('icon--vote')) {
        // Update comment fields
        field.plus = parseInt(field.plus) + 1;
        field.vote = 'plus';

        let url = `/id/ratings?comment=${field.id}&vote=plus`;

        makeRequest(url, 'POST', {}, (response) => {
          if (response.message) {
            console.error(response.message);
          }
        });
      }

      // Clear child nodes
      while (parent.firstChild) {
        parent.removeChild(parent.lastChild);
      }

      // Redraw votes block
      updateVotes(parent, field);
    });

    // Capture click on minus
    minus.addEventListener('click', (e) => {
      e.preventDefault();

      // Check if current vote is minus
      if (minus.classList.contains('icon--voted')) {
        // Update comment fields
        field.minus = parseInt(field.minus) - 1;
        delete field.vote;

        let url = `/id/ratings?comment=${field.id}&vote=minus`;

        makeRequest(url, 'DELETE', (response) => {
          if (response.message) {
            console.error(response.message);
          }
        });
      }

      // Check if current vote is not minus
      if (minus.classList.contains('icon--vote')) {
        // Update comment fields
        field.minus = parseInt(field.minus) + 1;
        field.vote = 'minus';

        let url = `/id/ratings?comment=${field.id}&vote=minus`;

        makeRequest(url, 'POST', (response) => {
          if (response.message) {
            console.error(response.message);
          }
        });
      }

      // Clear child nodes
      while (parent.firstChild) {
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

    // Draw avatar
    drawAvatar(item, field);

    // Create header
    let header = document.createElement('div');
    header.classList.add('comments__item-header');
    item.appendChild(header);

    // Set default name
    field.name = field.name || getOption('anonymous');

    // Create name
    let name = document.createElement('span');
    name.classList.add('comments__item-name');
    name.textContent = field.name;
    header.appendChild(name);

    // Draw time
    drawTime(header, field, item);

    // Create content
    let content = document.createElement('div');
    content.classList.add('comments__item-content');
    content.innerHTML = field.content;
    item.appendChild(content);

    // Create footer
    let footer = document.createElement('div');
    footer.classList.add('comments__item-footer');
    item.appendChild(footer);

    // Show reply button
    drawReply(footer, field, item);

    // Create vote element
    let vote = document.createElement('div');
    vote.classList.add('comments__item-vote');
    footer.appendChild(vote);

    // Update votes block
    updateVotes(vote, field)

    // Show manage buttons
    if (getOption('action')) {
      // Block and delete buttons for admins
      drawManage(footer, field);
    } else if (field.self) {
      // Remove button for user comments
      drawRemove(footer, field, item);
    }

    return item;
  }


  /**
   * Show comment
   */
  const loadComment = (field, show) => {
    let item = document.createElement('div');
    item.classList.add('comments__item');

    // Append comment elements to item
    item = drawComment(item, field);

    if (field.parent === null) {
      comments.appendChild(item);

      if (typeof show !== 'undefined' && show) {
        scrollToElement(item.getBoundingClientRect().top);
      }

      return false;
    }

    // Try to find parent
    let parent = comments.querySelector(`[data-id="${field.parent}"]`);

    if (parent === null) {
      comments.appendChild(item);

      if (typeof show !== 'undefined' && show) {
        scrollToElement(item.getBoundingClientRect().top);
      }

      return false;
    }

    let name = parent.querySelector('.comments__item-name');
    name

    // Find or create children list
    let children = parent.querySelector('.comments__item-children');

    if (children === null) {
      children = document.createElement('div');
      children.classList.add('comments__item-children');
      parent.appendChild(children);
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

    // Append item to children
    children.appendChild(item);

    if (typeof show !== 'undefined' && show) {
      scrollToElement(item.getBoundingClientRect().top);
    }
  }


  /**
   * Show specific comment
   */
  const showComment = (hash) => {
    let id = hash.replace('#comment-', '');

    // Try to find comment with id
    let comment = comments.querySelector(`[data-id="${id}"]`);

    if (comment !== null) {
      scrollToElement(comment.getBoundingClientRect().top);
    }
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
    let text = form.querySelector('textarea');

    makeRequest(url, 'POST', {'content': text.value}, (response) => {
      if (response.message) {
        console.error(response.message);
      }

      if (response.fields.length > 0) {
        form.reset();

        if (reply) {
          form.remove();
        }

        loadComment(response.fields[0], true);
      }
    });
  }


  /**
   * Create form
   */
  const createForm = () => {
    let form = document.createElement('form');
    form.classList.add('comments__form');

    let text = document.createElement('textarea');
    text.classList.add('comments__form-text');
    text.setAttribute('placeholder', getOption('placeholder'));
    text.setAttribute('required', 'required');
    form.append(text);

    let submit = document.createElement('button');
    submit.classList.add('comments__form-submit', 'button');
    submit.setAttribute('type', 'submit');
    submit.textContent = getOption('submit');
    form.appendChild(submit);

    // Submit form listener
    form.addEventListener('submit', (e) => {
      e.preventDefault();

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
      if(e.keyCode == 13 && (e.metaKey || e.ctrlKey)) {
        submit.click();
      }

      resizeForm();
    });

    // Resize on paste
    text.addEventListener('paste', resizeForm);

    return form;
  }


  /**
   * Make AJAX request
   */
  const makeRequest = (url, method, data, callback) => {
    const request = new XMLHttpRequest();
    request.open(method, url, true);
    request.setRequestHeader("Content-Type", "application/json");
    request.setRequestHeader("Authorization", "Bearer eyJhbGciOiJIUzI1NiJ9.MTE5OTk.QyEBvqipT1VU7Vnfl14qu8-qAbxrNVi61zCEUGyJ4Js");

    request.onload = () => {
      if (request.status === 200) {
        return callback(JSON.parse(request.responseText));
      }

      console.error(`Error while ${url} loading`);
    }

    request.send(JSON.stringify(data));
  }


  /**
   * Show comments form
   */
  let form = createForm();
  comments.appendChild(form);


  /**
   * Load comments
   */
  makeRequest(`/id/comments?post=${post}`, 'GET', {}, (response) => {
    const fields = response.fields || [];

    // Show comments using response fields
    fields.forEach(field => {
      loadComment(field);
    });

    // Show specific comment if loaded
    showComment(document.location.hash);
  });
})();
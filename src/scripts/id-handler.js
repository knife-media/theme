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
  const drawReply = (parent, field) => {
    let reply = document.createElement('button');
    reply.classList.add('comments__item-button');
    reply.textContent = getOption('reply');

    reply.addEventListener('click', (e) => {
      e.preventDefault();

      let form = document.createElement('textarea');
      //item.appendChild(form);


      parent.parentNode.insertBefore(form, parent.nextSibling);
    })

    parent.appendChild(reply);
  }


  /**
   * Draw comment remove button
   */
  const drawRemove = (parent, field) => {
    let remove = document.createElement('button');
    remove.classList.add('comments__item-button');
    remove.textContent = getOption('remove');

    remove.addEventListener('click', (e) => {
      e.preventDefault();
    })

    parent.appendChild(remove);
  }


  /**
   * Draw user block button
   */
  const drawBlock = (parent, field) => {
    let block = document.createElement('button');
    block.classList.add('comments__item-button');
    block.textContent = getOption('block');

    block.addEventListener('click', (e) => {
      e.preventDefault();
    })

    parent.appendChild(block);
  }


  /**
   * Draw votes block
   */
  const drawVotes = (parent, field) => {
    let vote = document.createElement('div');
    vote.classList.add('comments__item-vote');

    let rating = parseInt(field.plus) + parseInt(field.minus);

    if (rating > 0) {
      vote.classList.add('comments__item-vote--plus');
    }

    if (rating < 0) {
      vote.classList.add('comments__item-vote--minus');
    }

    let plus = document.createElement('button');
    plus.classList.add('icon', 'icon--vote');
    vote.appendChild(plus);

    // Check voted for plus
    if (field.vote === 'plus') {
      plus.classList.remove('icon--vote');
      plus.classList.add('icon--voted');
    }

    // Capture click on plus
    plus.addEventListener('click', (e) => {
      e.preventDefault();

      console.log(rating);
      rating = rating + 1;

      makeRequest(`/id/ratings?comment=${field.id}&vote=plus`, 'POST', (response) => {
        console.log(response);
      });
    });

    let count = document.createElement('span');
    count.textContent = rating;
    vote.appendChild(count);

    let minus = document.createElement('button');
    minus.classList.add('icon', 'icon--vote');
    vote.appendChild(minus);

    // Check voted for minus
    if (field.vote === 'minus') {
      minus.classList.remove('icon--vote');
      minus.classList.add('icon--voted');
    }

    // Capture click on minus
    minus.addEventListener('click', (e) => {
      e.preventDefault();

      alert('minus');
    });

    parent.appendChild(vote);
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
    drawReply(footer, field);

    // Show vote block
    drawVotes(footer, field);

    // Show remove button
    if (getOption('action') || field.self) {
      drawRemove(footer, field);
    }

    // Show block button
    if (getOption('action')) {
      drawBlock(footer, field);
    }

    return item;
  }


  /**
   * Show comment
   */
  const loadComment = (field) => {
    let item = document.createElement('div');
    item.classList.add('comments__item');

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
    if (level > 3) {
      children.classList.add('comments__item-children--deepest');
    }

    children.appendChild(item);
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
   * Make AJAX request
   */
  const makeRequest = (url, method, callback) => {
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

    request.send();
  }


  /**
   * Load comments
   */
  makeRequest('/id/comments?post=91039', 'GET', (response) => {
    const fields = response.fields || [];

    // Show required forms
    //loadPopup();

    // Show comments using response fields
    fields.forEach(field => {
      loadComment(field);
    });

    // Show specific comment if loaded
    showComment(document.location.hash);
  });
})();
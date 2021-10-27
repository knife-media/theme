/**
 * Add to all nouns satan adjective
 *
 * @since 1.15
 */

(function () {
  // Check if lables exists
  if (typeof knife_satanize === 'undefined') {
    return false;
  }

  const label = knife_satanize.button.split(' ');
  knife_satanize.button = label[0] + '<strong>' + label[1] + '</strong>';

  const selectors = [
    '.widget-single .widget-single__content-title',
    '.widget-cents .widget-cents__link',
    '.widget-story .widget-story__title-link',
    '.widget-blunt .widget-blunt__link',
    '.widget-transparent .widget-transparent__content-link',
    '.widget-club .widget-club__content-link',
    '.widget-recent .widget-recent__content-link',
    '.widget-news .widget-news__content-link',
    '.unit .unit__content-link',
    '.entry-header .entry-header__lead',
    '.entry-header .entry-header__title',
    '.entry-header .entry-header__link p',
    '.entry-content p',
    '.entry-content h1',
    '.entry-content h2',
    '.entry-content h3',
    '.entry-content h4',
    '.entry-content h5',
  ];

  let headings = [];
  let backdrop = document.querySelector('backdrop');

  if (backdrop === null) {
    backdrop = document.createElement('div');
    backdrop.classList.add('backdrop');
    document.body.appendChild(backdrop);
  }

  const button = document.createElement('button');
  button.classList.add('promo', 'promo--satanization');
  button.innerHTML = knife_satanize.button;
  document.body.appendChild(button);

  function getHeadings() {
    for (let i = 0, k = 0; i < selectors.length; i++) {
      const tags = document.querySelectorAll(selectors[i]);

      for (let j = 0; j < tags.length; j++, k++) {
        tags[j].setAttribute('data-satanization', k);

        // Save tags html to text
        headings[k] = tags[j].innerHTML;
      }
    }

    return headings;
  }

  function setSatanized(path) {
    headings = getHeadings();

    const request = new XMLHttpRequest();
    request.open('POST', '/satanization/', true);
    request.setRequestHeader("Content-Type", "application/json");

    request.onload = function () {
      button.innerHTML = knife_satanize.button;

      if (request.status !== 200) {
        return console.error('Error while /satanization/ loading');
      }

      const response = JSON.parse(request.responseText);

      if (!response.results) {
        return alert('excludes');
      }

      for (let i = 0; i < response.results.length; i++) {
        const find = document.querySelector('[data-satanization="' + i + '"]');

        find.innerHTML = response.results[i];
      }

      button.innerHTML = knife_satanize.getback || '';

      backdrop.setAttribute('data-image', backdrop.style.backgroundImage);
      backdrop.style.backgroundImage = 'linear-gradient(to bottom,#3b2307,#931111,#e62020)';
    }

    button.classList.add('promo--freeze');
    button.innerHTML = knife_satanize.process || '';

    const data = {
      'headings': headings,
      'path': path,
    }

    request.send(JSON.stringify(data));
  }

  // Handle button click
  button.addEventListener('click', function(e) {
    e.preventDefault();

    if (headings.length === 0) {
      return setSatanized(document.location.pathname);
    }

    for (let i = 0; i < headings.length; i++) {
      const find = document.querySelector('[data-satanization="' + i + '"]');

      find.innerHTML = headings[i];
    }

    headings = [];

    button.classList.remove('promo--freeze');
    button.innerHTML = knife_satanize.button || '';
    backdrop.style.backgroundImage = backdrop.getAttribute('data-image');
  });
})();

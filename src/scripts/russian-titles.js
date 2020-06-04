/**
 * Add to all nouns russin adjective
 *
 * @since 1.13
 */

(function () {
  var selectors = [
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

  var data = [], k = 0;

  for (var i = 0; i < selectors.length; i++) {
    var tags = document.querySelectorAll(selectors[i]);

    for (var j = 0; j < tags.length; j++, k++) {
      tags[j].setAttribute('data-russify', k);

      // Save tags html to text
      data[k] = tags[j].innerHTML;
    }
  }

  // Send request
  var request = new XMLHttpRequest();
  request.open('POST', '/russify/', true);
  request.setRequestHeader("Content-Type", "application/json");
  request.onload = function () {
    if (request.status !== 200) {
      return console.error('Error while /russify/ loading');
    }

    var response = JSON.parse(request.responseText);

    if (!response.results) {
      return console.error('Can not parse /russify/ response');
    }

    return false;

    for (var i = 0; i < response.results.length; i++) {
      var find = document.querySelector('[data-russify="' + i + '"]');

      find.innerHTML = response.results[i];
    }
  }

  request.send(JSON.stringify(data));
})();
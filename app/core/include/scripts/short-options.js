jQuery(document).ready(function ($) {
  if (typeof knife_short_options === 'undefined') {
    return;
  }

  const input = document.getElementById(knife_short_options.selector);

  // Find button right after input
  const button = $(input).next('button');

  button.on('click', function (e) {
    e.preventDefault();

    let value = $(input).val();
    value = value.replace('http://', 'https://', value);

    const query = {};

    try {
      const url = new URL(value);

      url.searchParams.forEach(function(value, key) {
        if (value === '' || key === 'pr') {
          return true;
        }

        query[key] = value;
      });

      let result = knife_short_options.source;

      if (value.substring(0, result.length) === result) {
        result = knife_short_options.destination;
      }

      const params = new URLSearchParams(query).toString();

      // Update input with new link
      $(input).val(result + '?' + params);
    } catch (e) {
      return console.error('Wrong link format');
    }
  });
});
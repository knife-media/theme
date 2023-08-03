/**
 * Append similar contents to single template
 *
 * @since 1.5
 * @version 1.15
 */

(function () {
  /**
   * Check if similar contents exist
   */
  if (typeof knife_similar_posts === 'undefined') {
    return false;
  }


  /**
   * Shuffle similar contents array
   */
  let similar = (function (items) {
    for (let i = 0, c; i < items.length; i++) {
      c = Math.floor(Math.random() * i);

      let temp = items[i];
      items[i] = items[c];
      items[c] = temp;
    }

    return items;
  }(knife_similar_posts.similar || []));


  /**
   * Define array counter
   *
   * @since 1.11
   */
  let counter = 0;

  /**
   * Update promo AdFox links.
   *
   * @since 1.15
   */
  const updatePromoLinks = (link) => {
    link = link.replace('http://ads.adfox.ru', 'https://ads.adfox.ru');
    link = link.replace('[RANDOM]', Date.now());

    return link;
  }


  /**
   * Create similar link
   *
   * @since 1.11
   */
  const appendLink = (wrap, similar) => {
    const item = document.createElement('p');
    wrap.appendChild(item);

    const link = document.createElement('a');
    link.href = updatePromoLinks(similar.link);
    link.innerHTML = similar.title;
    item.appendChild(link);

    if (similar.pixel) {
      let pixel = new Image();
      pixel.src = updatePromoLinks(similar.pixel);
    }
  }


  /**
   * Create similar block
   */
  const createSimilar = (following) => {
    if (similar.length < counter + 2) {
      return false;
    }

    // Let's insert similar posts if following found
    if (following === null) {
      return false;
    }

    const figure = document.createElement('figure');
    figure.classList.add('figure', 'figure--similar');

    const title = document.createElement('h4');
    title.innerHTML = knife_similar_posts.title || '';
    figure.appendChild(title);

    // Add similar items
    for (let i = counter; i < counter + 2; i++) {
      if (!similar[i].link || !similar[i].title) {
        continue;
      }

      // Create similar links
      appendLink(figure, similar[i]);
    }

    counter = counter + 2;

    // Insert block
    following.parentNode.insertBefore(figure, following.nextSibling);

    return true;
  }


  /**
   * Add footer similar links at first
   *
   * @since 1.11
   */
  (function () {
    const footer = document.querySelector('.entry-footer');

    // Skip if no footer or similar to short
    if (footer === null || similar.length < counter + 4) {
      return false;
    }

    const widget = document.createElement('div');
    widget.classList.add('entry-footer__similar');

    // Add similar items
    for (let i = counter; i < counter + 4; i++) {
      if (!similar[i].link || !similar[i].title) {
        continue;
      }

      // Create similar links
      appendLink(widget, similar[i]);
    }

    counter = counter + 4;

    // Append widget to footer
    footer.appendChild(widget);
  })();


  /**
   * Create auto similar contents if need
   */
  (function () {
    // Check if hidden is not true
    const content = document.querySelectorAll('.entry-content');

    // Skip cards and posts with hidden meta
    if (content.length !== 1 || knife_similar_posts.hidden == 1) {
      return false;
    }

    let children = content[0].children;

    // Check if entry-content long enough and similar links exist
    if (children.length < 20) {
      return false;
    }

    // Find start point
    const landmark = 20;

    for (let i = landmark, iterate = 0; i < children.length - 5; i++) {
      if (children[i].tagName.toLowerCase() !== 'p') {
        continue;
      }

      if (typeof children[i - 1] === 'undefined') {
        continue;
      }

      // Check if prev tag in allowed list
      if (children[i - 1].tagName.toLowerCase() !== 'p') {
        continue;
      }

      if (createSimilar(children[i - 1])) {
        iterate = iterate + 1;
      }

      // Maximum 3 similar on page
      if (iterate > 3) {
        break;
      }

      // Increase i by landmark
      i = landmark + i;
    }
  })();
})();

/**
 * Smart display entry inpost widgets
 *
 * @since 1.9
 * @version 1.14
 */

(function () {
  const content = document.querySelector('.entry-content');


  /**
   * Check if entry-content exists and it's long enough
   */
  if (content === null) {
    return false;
  }


  const widgets = document.querySelector('.entry-inpost');

  /**
   * Check if entry-widgets block has at least one widget
   */
  if (widgets === null || widgets.firstElementChild === null) {
    return false;
  }

  /**
   * Array of appropriate tags
   */
  const allowed = ['p'];

  /**
   * Find all appropriate children
   */
  const children = content.children;

  /**
   * Find post landmark
   */
  let landmark = Math.floor(children.length / 3);

  if (landmark < 6) {
    landmark = 6;
  }

  // Create aside
  const aside = document.createElement('aside');
  aside.classList.add('aside', 'aside--widget');
  aside.appendChild(widgets.firstElementChild);

  /**
   * Try to move first widget to content landmark
   */
  for (let i = landmark; i < children.length; i++) {
    // Check if next tag in allowed list
    if (allowed.indexOf(children[i].tagName.toLowerCase()) < 0) {
      continue;
    }

    if (typeof children[i - 1] === 'undefined') {
      continue;
    }

    let following = children[i - 1];

    // Check if prev tag in allowed list
    if (allowed.indexOf(following.tagName.toLowerCase()) < 0) {
      continue;
    }

    content.insertBefore(aside, children[i]);
    break;
  }

  if ( aside.parentNode !== content) {
    content.appendChild(aside);
  }
})();
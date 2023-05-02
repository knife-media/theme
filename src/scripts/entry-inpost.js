/**
 * Smart display entry inpost widgets
 *
 * @since 1.9
 * @version 1.17
 */

(function () {
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
   * Get first entry-content block
   */
  const blocks = document.querySelectorAll('.entry-content');

  if (blocks.length === 0) {
    return false;
  }

  let content = blocks[0];

  if (blocks.length > 1) {
    content = blocks[Math.floor(blocks.length / 2)];
  }

  /**
   * Find all appropriate children
   */
  const children = content.children;

  /**
   * Find post landmark
   */
  let landmark = Math.max(Math.floor(children.length / 3), 6);

  if (blocks.length > 1) {
    landmark = children.length;
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

    const following = children[i - 1];

    // Check if prev tag in allowed list
    if (allowed.indexOf(following.tagName.toLowerCase()) < 0) {
      continue;
    }

    content.insertBefore(aside, children[i]);
    break;
  }

  if (aside.parentNode !== content) {
    content.appendChild(aside);
  }
})();

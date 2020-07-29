(function () {
  /**
   * Check if custom options defined
   */
  if (typeof knife_theme_custom === 'undefined') {
    return false;
  }

  // Create format expand button
  let expand = document.createElement('button');
  expand.classList.add('button');
  expand.textContent = knife_theme_custom.formats || '';

  // Try to find formats section
  let formats = document.querySelector('.section--formats');

  if (formats !== null) {
    formats.appendChild(expand);
  }

  // Trigger on expanc click
  expand.addEventListener('click', (e) => {
    e.preventDefault();

    formats.querySelectorAll('.figure--ticket').forEach(ticket => {
      ticket.style.display = 'flex';
    });

    expand.remove();
  });
})();
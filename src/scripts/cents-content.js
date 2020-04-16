/**
 * Add cents posts handler
 *
 * @since 1.12
 */

(function() {
  if(typeof knife_cents_cards === 'undefined') {
    return false;
  }

  if(typeof knife_cents_options === 'undefined') {
    return false;
  }

  var post = document.querySelector('.post--cents');

  if(post === null) {
    return false;
  }

  /**
   * Replace new lines with paragraph
   */
  function nl2p(string) {
    // Replace \n\n with paragraph
    string = string.split('\n\n').join('</p><p>');

    // Replace single \n with break line tag
    string = string.split('\n').join('<br>');

    return '<p>' + string + '</p>';
  }


  /**
   * Compose cents card
   */
  function appendCard(item, card, counter) {
    card.innerHTML = nl2p(item.content);

    var title = document.createElement('h2');
    title.innerHTML = item.title;
    title.setAttribute('data-counter', '#' + counter);
    card.insertBefore(title, card.firstElementChild);

    // Append source button if exists
    if(item.source && item.link) {
      var button = document.createElement('a');
      button.classList.add('button');
      button.setAttribute('href', item.link);
      button.setAttribute('target', '_blank');
      button.setAttribute('rel', 'noopener');
      button.textContent = item.source;

      if(knife_cents_options.label) {
        var label = document.createElement('span');
        label.textContent = knife_cents_options.label;
        button.insertBefore(label, button.firstChild);
      }

      var figure = document.createElement('figure');
      figure.classList.add('figure', 'figure--source');
      figure.appendChild(button);

      card.appendChild(figure);
    }
  }

  var header = post.querySelector('.entry-header');

  /**
   * Create cards
   */
  for(var i = 0; i < knife_cents_cards.length; i++) {
    var card = document.createElement('div');
    card.classList.add('entry-cents');
    post.insertBefore(card, header);

    // Append card content to item
    appendCard(knife_cents_cards[i], card, knife_cents_cards.length - i);
  }
})();

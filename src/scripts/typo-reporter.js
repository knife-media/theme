/**
 * Typo reporter
 *
 * @since 1.12
 */

(function() {
  /**
   * Show reporter popup
   */
  function showPopup(selection) {
    var popup = document.createElement('div');
    popup.classList.add('typo');
    document.body.appendChild(popup);

    var button = document.createElement('button');
    button.classList.add('typo__button');
    button.textContent = 'Отправить';
    popup.appendChild(button);

    console.log(selection);
  }


  /**
   * Event listener on keydown
   */
  document.addEventListener('keydown', function(e) {
    if(event.ctrlKey && event.keyCode == 13) {
      var selection = window.getSelection().toString();

      // If selection not empty
      if(selection.length > 0) {
        showPopup(selection);
      }
    }
  });
})();

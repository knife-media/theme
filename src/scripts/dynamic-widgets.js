/**
 * Dynamic widgets handler
 *
 * @since 1.11
 */
(function() {
  var sidebar = document.querySelector('.sidebar');


  /**
   * Check sidebar element
   */
  if(sidebar === null) {
    return false;
  }

  var widgets = sidebar.querySelectorAll('.sidebar__widgets > div');


  /**
   * Check if widgets exists
   */
  if(widgets.length < 1) {
    return false;
  }


  /**
   * Global sidebar dynamic function to refresh sticky
   */
  window.dynamicWidgets = function() {
    console.log('sidebar dynamic')
  }


  var wrapper = document.createElement('div');
  wrapper.classList.add('sidebar__dynamic');


  /**
   * Find all dynamic widgets
   */
  for(var i = 0; i < widgets.length; i++) {
    var dynamic = ['widget-adfox', 'widget-script'];

    for(var j = 0; j < dynamic.length; j++) {
      if(widgets[i].classList.contains(dynamic[j])) {
        wrapper.appendChild(widgets[i]);
        break;
      }
    }
  }


  /**
   * Quit on empty scripts set
   */
  if(wrapper.firstElementChild) {
    sidebar.insertBefore(wrapper, sidebar.lastElementChild);
  }
})()

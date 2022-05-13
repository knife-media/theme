/**
 * Share buttons manager
 *
 * @version 1.15
 */

(function () {
  let analytics = false;

  /**
   * Open share popup window
   */
  const openPopup = function (url, params) {
    let left = Math.round(screen.width / 2 - params.width / 2);
    let top = 0;

    if (screen.height > params.height) {
      top = Math.round(screen.height / 3 - params.height / 2);
    }

    window.open(url, params.id, 'left=' + left + ',top=' + top + ',' +
      'width=' + params.width + ',height=' + params.height + ',personalbar=0,toolbar=0,scrollbars=1,resizable=1');
  }


  /**
   * Create share button counter
   */
  const createCounter = (link, counter) => {
    let child = link.querySelector('.share__count');

    if (null !== child) {
      return;
    }

    counter = parseInt(counter) || 0;

    if (counter <= 0) {
      return;
    }

    child = document.createElement('span');
    child.className = 'share__count';
    child.innerHTML = counter;

    link.appendChild(child);
  };


  /**
   * Show shares data on social buttons.
   */
  const showShares = (data) => {
    data = data || {};

    const links = document.querySelectorAll('.share .share__link');

    if (links === null) {
      return false;
    }

    links.forEach((link) => {
      const network = link.dataset.label;

      if (data.vk && network === 'vkontakte') {
        createCounter(link, data.vk);
      }

      if (data.fb && network === 'facebook') {
        createCounter(link, data.fb);
      }
    });
  };


  /**
   * Get share counters
   */
  const getShares = () => {
    if (undefined === knife_meta_parameters) {
      return;
    }

    if (!knife_meta_parameters.postid) {
      return;
    }

    const request = new XMLHttpRequest();
    request.open('GET', '/analytics/shares/?post=' + knife_meta_parameters.postid);
    request.responseType = 'json';
    request.send();

    request.addEventListener('load', () => {
      const response = request.response || {};

      if (!response.success) {
        return;
      }

      showShares(response.data);
    });

    return true;
  }


  /**
   * Global share buttons function
   */
  window.shareButtons = function () {
    const links = document.querySelectorAll('.share .share__link');

    if (links === null) {
      return false;
    }

    links.forEach((link) => {
      const network = link.dataset.label;

      link.addEventListener('click', function (e) {
        e.preventDefault();

        return openPopup(this.href, {
          width: 600,
          height: 400,
          id: this.dataset.label
        })
      });
    });

    getShares();
  }

  return window.shareButtons();
})();

(function() {
  if(typeof knife_push === 'undefined')
    return false;

  OneSignal = window.OneSignal || [];

  var initOneSignal = function() {
    var onesignal = document.createElement('script');
    onesignal.type = 'text/javascript';
    onesignal.async = true;
    onesignal.src = 'https://cdn.onesignal.com/sdks/OneSignalSDK.js';

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(onesignal, s);
  }


  var checkStorage = function() {
    var ls = localStorage.getItem('knife-push');

    if(typeof Notification === 'undefined' || Notification.permission !== "default") {
      return false;
    }

    if(typeof ls === "string" && Number(ls) < Date.now()) {
      return true;
    }

    if(ls === null) {
      localStorage.setItem('knife-push', Date.now());
    }

    return false;
  }


  var createPopup = function() {
    var popup = document.createElement('div');
    popup.classList.add('push', 'push--hide');
    document.body.appendChild(popup);

    // Append close button
    (function() {
      var close = document.createElement('button');
      close.classList.add('push__close');
      close.setAttribute('data-action', 'Push Notifications — Decline');

      popup.appendChild(close);
    })();


    // Append promo element
    (function(){
      var promo = document.createElement('div');
      promo.classList.add('push__promo');

      if(typeof knife_push.promo !== 'undefined') {
        promo.innerHTML = knife_push.promo;
      }

      popup.appendChild(promo);
    })();


    // Append accept button
    (function(){
      var button = document.createElement('button');
      button.classList.add('push__button');
      popup.appendChild(button);

      if(typeof knife_push.button !== 'undefined') {
        button.innerHTML = knife_push.button;
      }

      var notify = document.createElement('span');
      notify.classList.add('icon', 'icon--notify');
      button.appendChild(notify);
    })();

    return popup;
  }

  document.addEventListener('DOMContentLoaded', function() {
    if(checkStorage() === false || typeof knife_push.appid === 'undefined') {
      return false;
    }

    var popup = createPopup();

    OneSignal.push(["init", {
      appId: knife_push.appid,
      autoRegister: false,
      welcomeNotification: {
        disable: true
      }
    }]);


    OneSignal.push(function() {
      // Trigger on close button click
      popup.querySelector('.push__close').addEventListener('click', function() {
        // 2 weeks
        var future = 86400 * 1000 * 14;

        localStorage.setItem('knife-push', Date.now() + future);

        return popup.classList.add('push--hide');
      });

      // Trigger on subscribe button click
      popup.querySelector('.push__button').addEventListener('click', function() {
        return OneSignal.registerForPushNotifications();
      });

      // Hide message on permission change
      OneSignal.on('notificationPermissionChange', function(permission) {
        return popup.classList.add('push--hide');
      });

      // Check whether push notifications supported to show popup
      if(OneSignal.isPushNotificationsSupported()) {
        return popup.classList.remove('push--hide');
      }
    });

    return initOneSignal();

  }, false);
 })();

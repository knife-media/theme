(function() {
  var parent = document.querySelector('.push');

  if(parent === null || typeof knife_push_id === 'undefined')
    return false;

  OneSignal = window.OneSignal || [];

  var init = function() {
    var onesignal = document.createElement('script');
    onesignal.type = 'text/javascript';
    onesignal.async = true;
    onesignal.src = 'https://cdn.onesignal.com/sdks/OneSignalSDK.js';

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(onesignal, s);
  }

  var check = function() {
    var ls = localStorage.getItem('knife-push');

     if(typeof Notification === 'undefined' || Notification.permission !== "default")
      return false;

    if(typeof ls === "string" && Number(ls) < Date.now())
      return true;

    if(ls === null)
      localStorage.setItem('knife-push', Date.now());

    return false;
  }

  document.addEventListener('DOMContentLoaded', function() {
    if(check() === false)
      return false;


    OneSignal.push(["init", {
      appId: knife_push_id,
      autoRegister: false,
      welcomeNotification: {
        disable: true
      }
    }]);


    OneSignal.push(function() {
      // Trigger on close button click
      parent.querySelector('.push__close').addEventListener('click', function() {
        // 2 weeks
        var future = 86400 * 1000 * 14;

        localStorage.setItem('knife-push', Date.now() + future);

        return parent.classList.add('push--hide');
      });

      // Trigger on subscribe button click
      parent.querySelector('.push__button').addEventListener('click', function() {
        return OneSignal.registerForPushNotifications();
      });

      // Hide message on permission change
      OneSignal.on('notificationPermissionChange', function(permission) {
        return parent.classList.add('push--hide');
      });

      // Check whether push notifications supported to show popup
      if(OneSignal.isPushNotificationsSupported())
        return parent.classList.remove('push--hide');
    });


    return init();

  }, false);
 })();

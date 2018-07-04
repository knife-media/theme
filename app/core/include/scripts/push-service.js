jQuery(document).ready(function($) {
  var box = $("#knife-push-box");

  var wait = function() {
    box.find('.button')
      .toggleClass('disabled')
      .prop('disabled', function(i, v) {return !v});

    box.find('.spinner').toggleClass('is-active');
  }

  box.on('click', '#knife-push-send', function(e) {
    e.preventDefault();
    box.find(".notice").remove();

    var send = $(this).parent();

    var data = {
      action: 'knife_push_send',
      post: box.data('post'),
      title: box.find('#knife-push-title').val(),
      message: box.find('#knife-push-message').val()
    }

    var xhr = $.ajax({method: 'POST', url: box.data('ajaxurl'), data: data}, 'json');

    xhr.done(function(answer) {
      wait();

      var status = $('<div />', {
        "class": "notice",
        "html": "<p>" + answer.data + "</p>",
        "css": {"background-color": "#f4f4f4", "margin-top": "15px"}
      });

      if(answer.success !== true)
        return status.prependTo(box).addClass('notice-error');

      send.hide();

      return status.prependTo(box).addClass('notice-success');
    });

    return wait();
  });
});

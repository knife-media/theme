jQuery(document).ready(function ($) {
  var box = $('#knife-question-box');

  box.find('.question-emojis span').on('click', function() {
    box.find('.question-avatar').val($(this).text());
  });
});
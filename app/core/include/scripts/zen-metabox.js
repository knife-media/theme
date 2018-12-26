jQuery(document).ready(function($) {
  var box = $("#knife-zen-box");

  /**
   * Set current time
   */
  box.on('click', 'a', function(e) {
    e.preventDefault();

    // Put publish time to hidden input
    var publish = $(this).data('publish');

    if(typeof publish !== 'undefined') {
      $('#knife-zen-publish').val(publish);
    }

    // Put datetime to adminside page
    var display = $(this).data('display');

    if(typeof display !== 'undefined') {
      $('#knife-zen-display').html(display);
    }
  });
});

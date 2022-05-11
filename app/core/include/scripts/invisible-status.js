jQuery(document).ready(function ($) {
  if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  if (typeof knife_invisible_status === 'undefined') {
    return false;
  }

  $('#post_status').append('<option value="invisible">' + knife_invisible_status.label + '</option>');

  if (knife_invisible_status.current === knife_invisible_status.target) {
    $('#publish').hide();

    // Set invisible label
    $('#post-status-display').html(knife_invisible_status.label);

    // Change option value
    $('#post_status').val(knife_invisible_status.current);
  }

  $('#post-status-select .save-post-status').click(function() {
    $('#publish').show();

    if ($('#post_status').val() === knife_invisible_status.target) {
      $('#publish').hide();
    }
  });
});

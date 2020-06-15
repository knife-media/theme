jQuery(document).ready(function ($) {
  var field = $('#email').closest('tr');

  // Remove required to avoid errors
  field.removeClass('form-required');

  // Remove required description
  field.find('.description').remove();

  // Remove confirmation checkbox
  $('#createuser #send_user_notification').prop('checked', false);
});
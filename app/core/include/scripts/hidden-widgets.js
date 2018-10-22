jQuery( function( $ ) {
  var widgets_shell = $('div#widgets-right');

  if(!widgets_shell.length || !$(widgets_shell).find('.widget-control-actions').length) {
    widgets_shell = $('form#customize-controls');
  }

  function moveWidgetVisibilityButton($widget) {
    var $displayOptionsButton = $widget.find('a.display-options').first();
    $displayOptionsButton.insertBefore($widget.find('input.widget-control-save'));

    // Widgets with no configurable options don't show the Save button's container.
    $displayOptionsButton.parent()
      .removeClass('widget-control-noform')
      .find('.spinner')
      .remove()
      .css('float', 'left')
      .prependTo($displayOptionsButton.parent());
  }

  $('.widget').each(function() {
    moveWidgetVisibilityButton($(this));
  });

  $(document).on('widget-added', function(e, $widget) {
    if($widget.find('div.widget-control-actions a.display-options').length === 0) {
      moveWidgetVisibilityButton($widget);
    }
  });

  widgets_shell.on('click.widgetconditions', 'a.add-condition', function(e) {
    e.preventDefault();

    var $condition = $(this).closest('div.condition');
    var $conditionClone = $condition.clone().insertAfter($condition);

    $conditionClone.find('select.conditions-rule-major').val('');
    $conditionClone.find('select.conditions-rule-minor').html('').attr('disabled');
    $conditionClone.find('span.conditions-rule-has-children').hide().html('');
  });

  widgets_shell.on('click.widgetconditions', 'a.display-options', function (e) {
    e.preventDefault();

    var $displayOptionsButton = $(this);
    var $widget = $displayOptionsButton.closest('div.widget');

    $widget.find('div.widget-conditional').toggleClass('widget-conditional-hide');
    $(this).toggleClass('active');
    $widget.toggleClass('expanded');

    if($(this).hasClass('active')) {
      return $widget.find('input[name=widget-conditions-visible]').val('1');
    }

    return $widget.find('input[name=widget-conditions-visible]').val('0');
  });

  widgets_shell.on('click.widgetconditions', 'a.delete-condition', function( e ) {
    e.preventDefault();

    var $condition = $( this ).closest('div.condition');

    if($condition.is(':first-child') && $condition.is(':last-child')) {
      $( this ).closest('div.widget').find('a.display-options').click();

      return $condition.find('select.conditions-rule-major').val('').change();
    }

    return $condition.detach();
  });

  widgets_shell.on('click.widgetconditions', 'div.widget-top', function() {
    var $widget = $( this ).closest('div.widget');
    var $displayOptionsButton = $widget.find('a.display-options');

    if($displayOptionsButton.hasClass('active')) {
      $displayOptionsButton.attr('opened', 'true');
    }

    if($displayOptionsButton.attr('opened')) {
      $displayOptionsButton.removeAttr('opened');
      $widget.toggleClass('expanded');
    }
  });

  $(document).on('change.widgetconditions', 'select.conditions-rule-major', function() {
    var $conditionsRuleMajor = $ (this);
    var $conditionsRuleMinor = $conditionsRuleMajor.siblings('select.conditions-rule-minor:first');
    var $conditionsRuleHasChildren = $conditionsRuleMajor.siblings('span.conditions-rule-has-children');

    if($conditionsRuleMajor.val()) {
      if($conditionsRuleMajor.val() !== 'page') {
        $conditionsRuleHasChildren.hide().html('');
      }

      $conditionsRuleMinor.html('').append(
        $('<option/>').text($conditionsRuleMinor.data('loading-text'))
      );

      var data = {
        action: 'widget_conditions_options',
        major: $conditionsRuleMajor.val()
      };

      return jQuery.post(ajaxurl, data, function(html) {
        $conditionsRuleMinor.html(html).removeAttr('disabled');
      });
    }

    $conditionsRuleMajor.siblings('select.conditions-rule-minor').attr('disabled', 'disabled').html('');
    $conditionsRuleHasChildren.hide().html('');
  });

  $(document).on('change.widgetconditions', 'select.conditions-rule-minor', function() {
    var $conditionsRuleMinor = $ (this);
    var $conditionsRuleMajor = $conditionsRuleMinor.siblings('select.conditions-rule-major');
    var $conditionsRuleHasChildren = $conditionsRuleMinor.siblings('span.conditions-rule-has-children');

    if($conditionsRuleMajor.val() === 'page') {
      var data = {
        action: 'widget_conditions_has_children',
        major: $conditionsRuleMajor.val(),
        minor: $conditionsRuleMinor.val()
      };

      return jQuery.post(ajaxurl, data, function(html) {
        $conditionsRuleHasChildren.html(html).show();
      });
    }

    $conditionsRuleHasChildren.hide().html('');
  });
});
